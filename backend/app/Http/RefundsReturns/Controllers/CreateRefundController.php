<?php

declare(strict_types=1);

namespace App\Http\RefundsReturns\Controllers;

use App\Application\RefundsReturns\Actions\CreateRefundAction;
use App\Domain\RefundsReturns\ValueObjects\RefundType;
use App\Http\Controllers\Controller;
use App\Http\RefundsReturns\Requests\StoreRefundRequest;
use App\Models\User;
use App\Support\Http\RefundResource;
use Illuminate\Http\JsonResponse;

final class CreateRefundController extends Controller
{
    public function __invoke(
        StoreRefundRequest $request,
        CreateRefundAction $action,
        int $saleId,
    ): JsonResponse {
        $validated = $request->validated();
        $type = RefundType::from((string) $validated['type']);

        /** @var list<array{sale_line_id: int, quantity: int}> $lines */
        $lines = array_map(
            static fn (array $line): array => [
                'sale_line_id' => (int) $line['sale_line_id'],
                'quantity' => (int) $line['quantity'],
            ],
            $validated['lines'] ?? [],
        );

        /** @var User $manager */
        $manager = $request->user();

        $refund = $action->execute(
            $saleId,
            $type,
            (string) $validated['reason'],
            $manager,
            $lines,
        );

        return response()->json([
            'data' => [
                'message' => 'Refund recorded.',
                'refund' => RefundResource::toArray($refund),
            ],
        ], 201);
    }
}
