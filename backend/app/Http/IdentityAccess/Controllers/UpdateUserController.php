<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\UpdateUserRequest;
use App\Support\Http\UserResource;
use Illuminate\Http\JsonResponse;

final class UpdateUserController extends Controller
{
    public function __invoke(
        UpdateUserRequest $request,
        UpdateUserAction $action,
        int $userId,
    ): JsonResponse {
        $actorId = (int) $request->user()->getAuthIdentifier();
        $user = $action->execute($userId, $request->validated(), $actorId);

        return response()->json([
            'data' => [
                'message' => 'User updated.',
                'user' => UserResource::toArray($user),
            ],
        ]);
    }
}
