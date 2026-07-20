<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\AddSaleLineAction;
use App\Application\Sales\Actions\ShowSaleAction;
use App\Application\Shared\Idempotency\IdempotencyGuard;
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
        IdempotencyGuard $idempotency,
    ): JsonResponse {
        $validated = $request->validated();
        $productId = (int) $validated['product_id'];
        $quantity = (int) $validated['quantity'];
        $storeId = (int) $request->attributes->get('store_id');
        $userId = $request->user()->id;
        $shiftId = (int) $request->attributes->get('cash_shift_id');

        return $idempotency->run(
            $request,
            'sales.line:'.$saleId,
            [
                'product_id' => $productId,
                'quantity' => $quantity,
            ],
            function () use ($addLine, $showSale, $saleId, $productId, $quantity, $storeId, $userId, $shiftId): JsonResponse {
                $addLine->execute(
                    $saleId,
                    $productId,
                    $quantity,
                    $storeId,
                    $userId,
                    $shiftId,
                );

                $sale = $showSale->execute(
                    $saleId,
                    $storeId,
                    $userId,
                    $shiftId,
                );

                return response()->json([
                    'data' => [
                        'message' => 'Line added.',
                        'sale' => SaleResource::toArray($sale),
                    ],
                ]);
            },
        );
    }
}
