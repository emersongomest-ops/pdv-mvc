<?php

declare(strict_types=1);

namespace App\Http\Payments\Controllers;

use App\Application\Payments\Actions\ReconcilePendingPaymentsAction;
use App\Http\Controllers\Controller;
use App\Support\Store\StoreContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReconcileOperationalPaymentsController extends Controller
{
    public function __invoke(
        Request $request,
        ReconcilePendingPaymentsAction $reconcile,
        StoreContext $storeContext,
    ): JsonResponse {
        $summary = $reconcile->execute($storeContext->current($request));

        return response()->json(['data' => $summary]);
    }
}
