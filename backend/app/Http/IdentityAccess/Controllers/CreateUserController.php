<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\StoreUserRequest;
use App\Support\Http\UserResource;
use Illuminate\Http\JsonResponse;

final class CreateUserController extends Controller
{
    public function __invoke(StoreUserRequest $request, CreateUserAction $action): JsonResponse
    {
        $user = $action->execute($request->validated());

        return response()->json([
            'data' => [
                'message' => 'User created.',
                'user' => UserResource::toArray($user),
            ],
        ], 201);
    }
}
