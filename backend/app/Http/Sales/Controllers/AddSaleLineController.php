<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\AddSaleLineAction;
use App\Application\Sales\Actions\ShowSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\AddSaleLineRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class AddSaleLineController extends Controller
{
    public function __invoke(
        AddSaleLineRequest $request,
        int $saleId,
        AddSaleLineAction $addLine,
        ShowSaleAction $showSale,
    ): JsonResponse {
        $validated = $request->validated();

        $addLine->execute(
            $saleId,
            (int) $validated['product_id'],
            (int) $validated['quantity'],
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        $sale = $showSale->execute(
            $saleId,
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Line added.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
