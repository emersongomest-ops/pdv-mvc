<?php

declare(strict_types=1);

namespace App\Http\CashShift\Controllers;

use App\Application\CashShift\Actions\CloseCashShiftAction;
use App\Domain\Shared\Money;
use App\Http\CashShift\Requests\CloseCashShiftRequest;
use App\Http\Controllers\Controller;
use App\Support\Http\CashShiftReportResource;
use Illuminate\Http\JsonResponse;

final class CloseCashShiftController extends Controller
{
    public function __invoke(CloseCashShiftRequest $request, CloseCashShiftAction $action): JsonResponse
    {
        $validated = $request->validated();
        $storeId = (int) $request->attributes->get('store_id');
        $closingAmount = array_key_exists('closing_cash_amount', $validated) && $validated['closing_cash_amount'] !== null
            ? Money::fromDecimalInput($validated['closing_cash_amount'])
            : null;

        $result = $action->execute($request->user(), $storeId, $closingAmount);
        $shift = $result['shift'];

        return response()->json([
            'data' => [
                'message' => 'Cash shift closed.',
                'shift' => [
                    'id' => $shift->id,
                    'store_id' => $shift->store_id,
                    'status' => $shift->status->value,
                    'closing_cash_amount' => $shift->closing_cash_amount !== null
                        ? Money::toDecimalString((int) $shift->closing_cash_amount)
                        : null,
                    'closed_at' => $shift->closed_at?->toIso8601String(),
                ],
                'report' => CashShiftReportResource::toArray($result['report']),
            ],
        ]);
    }
}
