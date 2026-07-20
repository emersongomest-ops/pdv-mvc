<?php

declare(strict_types=1);

namespace App\Http\CashShift\Controllers;

use App\Application\CashShift\Actions\ListAdminShiftsAction;
use App\Domain\Shared\Money;
use App\Http\CashShift\Requests\ListAdminShiftsRequest;
use App\Http\Controllers\Controller;
use App\Models\CashShift;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class ListAdminShiftsController extends Controller
{
    public function __invoke(ListAdminShiftsRequest $request, ListAdminShiftsAction $action): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $storeId = (int) $request->validated('store_id');
        $shifts = $action->execute($manager, $storeId);

        return response()->json([
            'data' => [
                'shifts' => $shifts
                    ->map(static function (CashShift $shift): array {
                        return [
                            'id' => $shift->id,
                            'store_id' => $shift->store_id,
                            'store_code' => $shift->store?->code,
                            'operator_id' => $shift->user_id,
                            'operator_name' => $shift->operator?->name,
                            'status' => $shift->status->value,
                            'opening_cash_amount' => Money::toDecimalString((int) $shift->opening_cash_amount),
                            'closing_cash_amount' => $shift->closing_cash_amount !== null
                                ? Money::toDecimalString((int) $shift->closing_cash_amount)
                                : null,
                            'opened_at' => $shift->opened_at?->toIso8601String(),
                            'closed_at' => $shift->closed_at?->toIso8601String(),
                        ];
                    })
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
