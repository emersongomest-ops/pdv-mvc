<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\ListUsersAction;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\ListUsersRequest;
use App\Models\User;
use App\Support\Http\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class ListUsersController extends Controller
{
    public function __invoke(ListUsersRequest $request, ListUsersAction $action): JsonResponse
    {
        $validated = $request->validated();
        $search = $validated['search'] ?? null;
        $cursor = $validated['cursor'] ?? null;
        $perPage = $validated['per_page'] ?? null;

        try {
            $result = $action->execute(
                is_string($search) && $search !== '' ? $search : null,
                is_string($cursor) && $cursor !== '' ? $cursor : null,
                $perPage !== null ? (int) $perPage : null,
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'cursor' => ['The cursor is invalid.'],
            ]);
        }

        $payload = [
            'data' => [
                'users' => $result['users']
                    ->map(static fn (User $user): array => UserResource::toArray($user))
                    ->values()
                    ->all(),
            ],
        ];

        if ($perPage !== null) {
            $payload['meta'] = [
                'next_cursor' => $result['next_cursor'],
            ];
        }

        return response()->json($payload);
    }
}
