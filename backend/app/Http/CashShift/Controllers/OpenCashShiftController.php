<?php

declare(strict_types=1);

namespace App\Http\CashShift\Controllers;

use App\Application\CashShift\Actions\OpenCashShiftAction;
use App\Domain\Shared\Money;
use App\Http\CashShift\Requests\OpenCashShiftRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class OpenCashShiftController extends Controller
{
    public function __invoke(OpenCashShiftRequest $request, OpenCashShiftAction $action): JsonResponse
    {
        $validated = $request->validated();
        $storeId = (int) $request->attributes->get('store_id');
        $openingAmount = isset($validated['opening_cash_amount'])
            ? Money::fromDecimalInput($validated['opening_cash_amount'])
            : 0;

        $shift = $action->execute($request->user(), $storeId, $openingAmount);

        return response()->json([
            'data' => [
                'message' => 'Cash shift opened.',
                'shift' => [
                    'id' => $shift->id,
                    'store_id' => $shift->store_id,
                    'status' => $shift->status->value,
                    'opening_cash_amount' => Money::toDecimalString((int) $shift->opening_cash_amount),
                    'opened_at' => $shift->opened_at?->toIso8601String(),
                ],
            ],
        ], 201);
    }
}
