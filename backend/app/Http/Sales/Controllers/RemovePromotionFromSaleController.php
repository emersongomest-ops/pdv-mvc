<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\RemovePromotionFromSaleAction;
use App\Http\Controllers\Controller;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RemovePromotionFromSaleController extends Controller
{
    public function __invoke(
        Request $request,
        RemovePromotionFromSaleAction $action,
        int $saleId,
        int $promotionId,
    ): JsonResponse {
        $sale = $action->execute(
            $saleId,
            $promotionId,
            (int) $request->attributes->get('store_id'),
            (int) $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Promotion removed.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
