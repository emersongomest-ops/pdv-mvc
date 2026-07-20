<?php

declare(strict_types=1);

namespace App\Http\Sales\Controllers;

use App\Application\Sales\Actions\ShowAdminSaleAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Http\SaleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShowAdminSaleController extends Controller
{
    public function __invoke(Request $request, int $saleId, ShowAdminSaleAction $action): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $sale = $action->execute($manager, $saleId);

        return response()->json([
            'data' => [
                'sale' => SaleResource::toArray($sale),
            ],
        ]);
    }
}
