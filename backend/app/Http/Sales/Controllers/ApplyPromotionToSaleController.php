<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\ApplyPromotionToSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\ApplyPromotionToSaleRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class ApplyPromotionToSaleController extends Controller
{
    public function __invoke(
        ApplyPromotionToSaleRequest $request,
        ApplyPromotionToSaleAction $action,
        int $saleId,
    ): JsonResponse {
        $sale = $action->execute(
            $saleId,
            (string) $request->validated('code'),
            (int) $request->attributes->get('store_id'),
            (int) $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Promotion applied.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
