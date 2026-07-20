<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\ShowSaleAction;
use App\Http\Controllers\Controller;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShowSaleController extends Controller
{
    public function __invoke(Request $request, int $saleId, ShowSaleAction $action): JsonResponse
    {
        $sale = $action->execute(
            $saleId,
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
