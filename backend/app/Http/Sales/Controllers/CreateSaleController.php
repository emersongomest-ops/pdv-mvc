<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\CreateSaleAction;
use App\Application\Shared\Idempotency\IdempotencyGuard;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\CreateSaleRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class CreateSaleController extends Controller
{
    public function __invoke(
        CreateSaleRequest $request,
        CreateSaleAction $action,
        IdempotencyGuard $idempotency,
    ): JsonResponse {
        $validated = $request->validated();
        $productId = isset($validated['product_id']) ? (int) $validated['product_id'] : null;
        $quantity = isset($validated['quantity']) ? (int) $validated['quantity'] : null;
        $storeId = (int) $request->attributes->get('store_id');

        return $idempotency->run(
            $request,
            'sales.create:'.$storeId,
            [
                'product_id' => $productId,
                'quantity' => $quantity,
            ],
            function () use ($action, $request, $storeId, $productId, $quantity): JsonResponse {
                $sale = $action->execute(
                    $request->user(),
                    $storeId,
                    (int) $request->attributes->get('cash_shift_id'),
                    $productId,
                    $quantity,
                );

                $withLine = $productId !== null;

                return response()->json([
                    'data' => [
                        'message' => $withLine ? 'Sale created with line.' : 'Sale created.',
                        'sale' => SaleResource::toArray($sale),
                    ],
                ], 201);
            },
        );
    }
}
