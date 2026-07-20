<?php

declare(strict_types=1);

namespace App\Http\Audit\Controllers;

use App\Application\Audit\Actions\ListAdminAuditLogsAction;
use App\Domain\Audit\DTOs\AdminAuditFilters;
use App\Http\Audit\Requests\ListAdminAuditLogsRequest;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\Http\AuditLogResource;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

final class ListAdminAuditLogsController extends Controller
{
    public function __invoke(
        ListAdminAuditLogsRequest $request,
        ListAdminAuditLogsAction $action,
    ): JsonResponse {
        $validated = $request->validated();

        $filters = new AdminAuditFilters(
            fromDate: isset($validated['from']) ? (string) $validated['from'] : null,
            toDate: isset($validated['to']) ? (string) $validated['to'] : null,
            action: isset($validated['action']) ? (string) $validated['action'] : null,
            actorUserId: isset($validated['actor_id']) ? (int) $validated['actor_id'] : null,
            storeId: isset($validated['store_id']) ? (int) $validated['store_id'] : null,
            subjectType: isset($validated['subject_type']) ? (string) $validated['subject_type'] : null,
            subjectId: isset($validated['subject_id']) ? (int) $validated['subject_id'] : null,
            cursor: isset($validated['cursor']) ? (string) $validated['cursor'] : null,
            perPage: isset($validated['per_page']) ? (int) $validated['per_page'] : 50,
        );

        /** @var User $manager */
        $manager = $request->user();

        try {
            $result = $action->execute($manager, $filters);
        } catch (InvalidArgumentException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'cursor' => ['The cursor is invalid.'],
                ],
            ], 422);
        }

        return response()->json([
            'data' => [
                'audit_logs' => $result['items']
                    ->map(static fn (AuditLog $log): array => AuditLogResource::toArray($log))
                    ->values()
                    ->all(),
            ],
            'meta' => [
                'next_cursor' => $result['next_cursor'],
            ],
        ]);
    }
}
