<?php

declare(strict_types=1);

namespace App\Http\RefundsReturns\Controllers;

use App\Application\RefundsReturns\Actions\ListRefundsForSaleAction;
use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\User;
use App\Support\Http\RefundResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListRefundsForSaleController extends Controller
{
    public function __invoke(Request $request, ListRefundsForSaleAction $action, int $saleId): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $refunds = $action->execute($manager, $saleId);

        return response()->json([
            'data' => [
                'refunds' => $refunds
                    ->map(fn (Refund $refund): array => RefundResource::toArray($refund))
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
