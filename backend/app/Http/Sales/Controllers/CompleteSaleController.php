<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\CompleteSaleAction;
use App\Domain\Shared\Money;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\CompleteSaleRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class CompleteSaleController extends Controller
{
    public function __invoke(
        CompleteSaleRequest $request,
        int $saleId,
        CompleteSaleAction $action,
    ): JsonResponse {
        $validated = $request->validated();

        /** @var list<array<string, mixed>> $payments */
        $payments = array_map(
            static function (array $payment): array {
                $normalized = [
                    'method' => (string) $payment['method'],
                    'amount' => Money::fromDecimalInput($payment['amount']),
                ];

                if (array_key_exists('cash_received', $payment) && $payment['cash_received'] !== null) {
                    $normalized['cash_received'] = Money::fromDecimalInput($payment['cash_received']);
                }

                if (isset($payment['card']) && is_array($payment['card'])) {
                    $normalized['card'] = [
                        'holder_name' => (string) $payment['card']['holder_name'],
                        'number' => (string) $payment['card']['number'],
                        'exp_month' => (int) $payment['card']['exp_month'],
                        'exp_year' => (int) $payment['card']['exp_year'],
                        'indicated_person_name' => (string) $payment['card']['indicated_person_name'],
                        'belongs_to_indicated_person' => (bool) $payment['card']['belongs_to_indicated_person'],
                    ];
                }

                return $normalized;
            },
            $validated['payments'],
        );

        $sale = $action->execute(
            $saleId,
            $payments,
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Sale completed.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
