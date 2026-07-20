<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\RemoveSaleLineAction;
use App\Http\Controllers\Controller;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RemoveSaleLineController extends Controller
{
    public function __invoke(
        Request $request,
        int $saleId,
        int $lineId,
        RemoveSaleLineAction $action,
    ): JsonResponse {
        $sale = $action->execute(
            $saleId,
            $lineId,
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Line removed.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
