<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\ShowSaleAction;
use App\Application\Sales\Actions\UpdateSaleLineAction;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\UpdateSaleLineRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class UpdateSaleLineController extends Controller
{
    public function __invoke(
        UpdateSaleLineRequest $request,
        int $saleId,
        int $lineId,
        UpdateSaleLineAction $updateLine,
        ShowSaleAction $showSale,
    ): JsonResponse {
        $validated = $request->validated();

        $updateLine->execute(
            $saleId,
            $lineId,
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
                'message' => 'Line updated.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
