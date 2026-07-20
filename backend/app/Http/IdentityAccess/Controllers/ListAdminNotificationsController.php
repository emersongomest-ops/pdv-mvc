<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

final class ListAdminNotificationsController extends Controller
{
    public function __invoke(
        Request $request,
        AssertManagerStoreAccess $storeAccess,
    ): JsonResponse {
        /** @var User|null $user */
        $user = $request->user();
        $assignedStoreIds = $user !== null
            ? $storeAccess->assignedStoreIds($user)
            : [];

        $notifications = $user
            ?->notifications()
            ->latest()
            ->limit(50)
            ->get()
            ->filter(static function (DatabaseNotification $notification) use ($assignedStoreIds): bool {
                /** @var array<string, mixed> $data */
                $data = $notification->data;

                if (! array_key_exists('store_id', $data) || $data['store_id'] === null) {
                    return true;
                }

                return in_array((int) $data['store_id'], $assignedStoreIds, true);
            })
            ->take(20)
            ->map(static function (DatabaseNotification $notification): array {
                /** @var array<string, mixed> $data */
                $data = $notification->data;

                return [
                    'id' => $notification->id,
                    'kind' => $data['kind'] ?? null,
                    'data' => $data,
                    'read_at' => $notification->read_at?->toIso8601String(),
                    'created_at' => $notification->created_at?->toIso8601String(),
                ];
            })
            ->values()
            ->all() ?? [];

        return response()->json([
            'data' => [
                'notifications' => $notifications,
            ],
        ]);
    }
}
