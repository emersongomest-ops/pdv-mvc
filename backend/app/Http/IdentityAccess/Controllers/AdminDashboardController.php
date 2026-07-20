<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\Admin\Actions\GetAdminDashboardMetricsAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminDashboardController extends Controller
{
    public function __invoke(
        Request $request,
        GetAdminDashboardMetricsAction $action,
    ): JsonResponse {
        /** @var User $manager */
        $manager = $request->user();
        $metrics = $action->execute($manager);

        return response()->json([
            'data' => [
                'area' => 'admin',
                'message' => 'Manager dashboard access granted.',
                'user_id' => $manager->id,
                'metrics' => $metrics->toArray(),
            ],
        ]);
    }
}
