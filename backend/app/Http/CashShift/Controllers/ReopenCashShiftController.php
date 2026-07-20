<?php

declare(strict_types=1);

namespace App\Http\CashShift\Controllers;

use App\Application\CashShift\Actions\ReopenCashShiftAction;
use App\Domain\Shared\Money;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReopenCashShiftController extends Controller
{
    public function __invoke(Request $request, int $shiftId, ReopenCashShiftAction $action): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $shift = $action->execute($manager, $shiftId);

        return response()->json([
            'data' => [
                'message' => 'Cash shift reopened.',
                'shift' => [
                    'id' => $shift->id,
                    'store_id' => $shift->store_id,
                    'status' => $shift->status->value,
                    'opening_cash_amount' => Money::toDecimalString((int) $shift->opening_cash_amount),
                    'closing_cash_amount' => $shift->closing_cash_amount !== null
                        ? Money::toDecimalString((int) $shift->closing_cash_amount)
                        : null,
                    'opened_at' => $shift->opened_at?->toIso8601String(),
                    'closed_at' => $shift->closed_at?->toIso8601String(),
                ],
            ],
        ]);
    }
}
