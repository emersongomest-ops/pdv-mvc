<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\ListHeldSalesAction;
use App\Http\Controllers\Controller;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListHeldSalesController extends Controller
{
    public function __invoke(Request $request, ListHeldSalesAction $action): JsonResponse
    {
        $sales = $action->execute(
            (int) $request->attributes->get('store_id'),
            $request->user()->id,
            (int) $request->attributes->get('cash_shift_id'),
        );

        return response()->json([
            'data' => [
                'sales' => $sales
                    ->map(fn ($sale): array => SaleResource::toArray($sale))
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
