<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\ResetManagerMfaAction;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\ResetManagerMfaRequest;
use App\Models\User;
use App\Support\Http\UserResource;
use Illuminate\Http\JsonResponse;

final class ResetManagerMfaController extends Controller
{
    public function __invoke(
        ResetManagerMfaRequest $request,
        ResetManagerMfaAction $action,
        int $userId,
    ): JsonResponse {
        /** @var User $actor */
        $actor = $request->user();

        $user = $action->execute(
            $userId,
            $actor,
            (string) $request->validated('reason'),
        );

        return response()->json([
            'data' => [
                'message' => 'Manager MFA reset. Target must re-enroll on next login.',
                'user' => UserResource::toArray($user),
            ],
        ]);
    }
}
