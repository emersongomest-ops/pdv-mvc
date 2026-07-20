<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\AttachCustomerToSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Sales\Requests\AttachCustomerToSaleRequest;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;

final class AttachCustomerToSaleController extends Controller
{
    public function __invoke(
        AttachCustomerToSaleRequest $request,
        AttachCustomerToSaleAction $action,
        int $saleId,
    ): JsonResponse {
        $sale = $action->execute(
            $saleId,
            (int) $request->validated('customer_id'),
            (int) $request->attributes->get('store_id'),
            (int) $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'message' => 'Customer attached to sale.',
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
