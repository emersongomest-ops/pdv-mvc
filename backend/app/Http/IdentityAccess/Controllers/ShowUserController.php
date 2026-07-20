<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\ShowUserAction;
use App\Http\Controllers\Controller;
use App\Support\Http\UserResource;
use Illuminate\Http\JsonResponse;

final class ShowUserController extends Controller
{
    public function __invoke(ShowUserAction $action, int $userId): JsonResponse
    {
        $user = $action->execute($userId);

        return response()->json([
            'data' => [
                'user' => UserResource::toArray($user),
            ],
        ]);
    }
}
