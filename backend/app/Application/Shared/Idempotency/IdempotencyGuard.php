<?php

declare(strict_types=1);

namespace App\Application\Shared\Idempotency;

use App\Domain\Shared\ErrorCode;
use App\Domain\Shared\Exceptions\IdempotencyDomainException;
use App\Domain\Shared\Idempotency\IdempotencyRecordRepositoryInterface;
use App\Models\IdempotencyRecord;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;
use Throwable;

/**
 * Claim / replay / conflict for Idempotency-Key (RN-073).
 */
final class IdempotencyGuard
{
    public const HEADER = 'Idempotency-Key';

    public const REPLAYED_HEADER = 'Idempotent-Replayed';

    public function __construct(
        private readonly IdempotencyRecordRepositoryInterface $records,
    ) {}

    public function requireKey(Request $request): string
    {
        $key = $request->headers->get(self::HEADER);

        if (! is_string($key) || $key === '' || strlen($key) > 128) {
            throw new IdempotencyDomainException(ErrorCode::IdempotencyKeyRequired);
        }

        if (preg_match('/^[A-Za-z0-9._:-]+$/', $key) !== 1) {
            throw new IdempotencyDomainException(ErrorCode::IdempotencyKeyRequired);
        }

        return $key;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hashPayload(array $payload): string
    {
        try {
            $json = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (JsonException) {
            throw new IdempotencyDomainException(ErrorCode::IdempotencyKeyRequired);
        }

        return hash('sha256', $json);
    }

    /**
     * @return array{outcome: 'proceed', record: IdempotencyRecord}|array{outcome: 'replay', record: IdempotencyRecord}
     */
    public function begin(
        string $key,
        string $scope,
        int $userId,
        string $requestHash,
        ?string $requestId,
    ): array {
        try {
            $record = $this->records->claimProcessing($scope, $key, $userId, $requestHash, $requestId);

            return ['outcome' => 'proceed', 'record' => $record];
        } catch (QueryException $exception) {
            if (! $this->isUniqueViolation($exception)) {
                throw $exception;
            }
        }

        $existing = $this->records->findByScopeAndKey($scope, $key);

        if ($existing === null) {
            throw new IdempotencyDomainException(ErrorCode::IdempotencyRequestInFlight);
        }

        if ($existing->user_id !== $userId) {
            throw new IdempotencyDomainException(ErrorCode::IdempotencyKeyReuse);
        }

        if ($existing->request_hash !== $requestHash) {
            throw new IdempotencyDomainException(ErrorCode::IdempotencyKeyReuse);
        }

        if ($existing->isCompleted()) {
            return ['outcome' => 'replay', 'record' => $existing];
        }

        throw new IdempotencyDomainException(ErrorCode::IdempotencyRequestInFlight);
    }

    /**
     * @param  array<string, mixed>  $responseBody
     */
    public function complete(IdempotencyRecord $record, int $responseCode, array $responseBody): void
    {
        $this->records->markCompleted($record, $responseCode, $responseBody);
    }

    public function abort(IdempotencyRecord $record): void
    {
        $this->records->delete($record);
    }

    public function replayResponse(IdempotencyRecord $record): JsonResponse
    {
        /** @var array<string, mixed> $body */
        $body = $record->response_body ?? [];

        return response()
            ->json($body, (int) $record->response_code)
            ->header(self::REPLAYED_HEADER, 'true');
    }

    /**
     * @param  callable(): JsonResponse  $callback
     * @param  array<string, mixed>  $payloadForHash
     */
    public function run(
        Request $request,
        string $scope,
        array $payloadForHash,
        callable $callback,
    ): JsonResponse {
        $key = $this->requireKey($request);
        $userId = (int) $request->user()?->getAuthIdentifier();
        $hash = $this->hashPayload($payloadForHash);
        $requestId = $request->attributes->get('request_id');
        $requestId = is_string($requestId) ? $requestId : null;

        $begin = $this->begin($key, $scope, $userId, $hash, $requestId);

        if ($begin['outcome'] === 'replay') {
            return $this->replayResponse($begin['record']);
        }

        $record = $begin['record'];

        try {
            $response = $callback();
            /** @var array<string, mixed> $payload */
            $payload = json_decode($response->getContent() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
            $this->complete($record, $response->getStatusCode(), $payload);

            return $response;
        } catch (Throwable $exception) {
            $this->abort($record);

            throw $exception;
        }
    }

    private function isUniqueViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = $exception->errorInfo[1] ?? null;

        // SQLSTATE 23000 + MySQL 1062 / SQLite UNIQUE
        if ($sqlState === '23000') {
            return true;
        }

        $message = $exception->getMessage();

        return str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'Duplicate entry')
            || $driverCode === 1062;
    }
}
