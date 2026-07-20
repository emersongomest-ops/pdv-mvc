<?php

declare(strict_types=1);

namespace App\Http\CashShift\Controllers;

use App\Application\CashShift\Actions\GetAdminShiftReportAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Http\CashShiftReportResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShowAdminShiftReportController extends Controller
{
    public function __invoke(Request $request, int $shiftId, GetAdminShiftReportAction $action): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $report = $action->execute($manager, $shiftId);

        return response()->json([
            'data' => [
                'report' => CashShiftReportResource::toArray($report),
            ],
        ]);
    }
}
