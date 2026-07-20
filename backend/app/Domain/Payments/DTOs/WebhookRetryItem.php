<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

/** Failed inbound webhook payload queued for retry (Option B). */
final readonly class WebhookRetryItem
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $provider,
        public string $rawBody,
        public ?string $signatureHeader,
        public array $payload,
        public int $attempts,
        public string $lastError,
        public string $enqueuedAt,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'raw_body' => $this->rawBody,
            'signature_header' => $this->signatureHeader,
            'payload' => $this->payload,
            'attempts' => $this->attempts,
            'last_error' => $this->lastError,
            'enqueued_at' => $this->enqueuedAt,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array<string, mixed> $payload */
        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        return new self(
            provider: (string) $data['provider'],
            rawBody: (string) $data['raw_body'],
            signatureHeader: isset($data['signature_header']) ? (string) $data['signature_header'] : null,
            payload: $payload,
            attempts: (int) ($data['attempts'] ?? 0),
            lastError: (string) ($data['last_error'] ?? ''),
            enqueuedAt: (string) ($data['enqueued_at'] ?? now()->toIso8601String()),
        );
    }
}
