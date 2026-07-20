<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\ShowCurrentUserAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ShowCurrentUserController extends Controller
{
    public function __invoke(Request $request, ShowCurrentUserAction $action): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $result = $action->execute($user);

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'role' => $result['user']->role->value,
                ],
            ],
        ]);
    }
}
