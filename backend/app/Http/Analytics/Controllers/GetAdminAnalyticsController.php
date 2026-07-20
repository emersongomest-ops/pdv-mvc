<?php

declare(strict_types=1);

namespace App\Http\Analytics\Controllers;

use App\Application\Analytics\Actions\GetAdminAnalyticsAction;
use App\Http\Analytics\Requests\GetAdminAnalyticsRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Http\AnalyticsResource;
use Illuminate\Http\JsonResponse;

final class GetAdminAnalyticsController extends Controller
{
    public function __invoke(GetAdminAnalyticsRequest $request, GetAdminAnalyticsAction $action): JsonResponse
    {
        /** @var User $manager */
        $manager = $request->user();
        $validated = $request->validated();
        $snapshot = $action->execute(
            $manager,
            (int) ($validated['registration_days'] ?? 30),
            (int) ($validated['top_customers'] ?? 20),
        );

        return response()->json([
            'data' => AnalyticsResource::snapshotToArray($snapshot),
        ]);
    }
}
