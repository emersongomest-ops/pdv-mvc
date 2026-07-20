<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\HoldSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\HoldSaleRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class HoldSaleController extends Controller
{
    public function __invoke(
        HoldSaleRequest $request,
        int $saleId,
        HoldSaleAction $action,
    ): JsonResponse {
        $label = $request->validated()['label'] ?? null;

        $sale = $action->execute(
            $saleId,
            is_string($label) ? $label : null,
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Sale parked on hold.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
