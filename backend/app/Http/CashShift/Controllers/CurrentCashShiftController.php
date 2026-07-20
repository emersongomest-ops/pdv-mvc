<?php

declare(strict_types=1);

namespace App\Http\CashShift\Controllers;

use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\Shared\Money;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CurrentCashShiftController extends Controller
{
    public function __invoke(Request $request, CashShiftRepositoryInterface $shifts): JsonResponse
    {
        $storeId = (int) $request->attributes->get('store_id');
        $shift = $shifts->findOpenForUserAtStore($request->user()->id, $storeId);

        if ($shift === null) {
            return response()->json([
                'data' => [
                    'shift' => null,
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'shift' => [
                    'id' => $shift->id,
                    'store_id' => $shift->store_id,
                    'status' => $shift->status->value,
                    'opening_cash_amount' => Money::toDecimalString((int) $shift->opening_cash_amount),
                    'opened_at' => $shift->opened_at?->toIso8601String(),
                ],
            ],
        ]);
    }
}
