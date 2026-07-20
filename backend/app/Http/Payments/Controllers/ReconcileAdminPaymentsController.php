<?php

declare(strict_types=1);

namespace App\Http\Payments\Controllers;

use App\Application\Payments\Actions\ReconcilePendingPaymentsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class ReconcileAdminPaymentsController extends Controller
{
    public function __invoke(ReconcilePendingPaymentsAction $reconcile): JsonResponse
    {
        $summary = $reconcile->execute(null);

        return response()->json(['data' => $summary]);
    }
}
