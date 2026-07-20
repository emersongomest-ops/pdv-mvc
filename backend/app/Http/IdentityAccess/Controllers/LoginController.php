<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\LoginUserAction;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserAction $login): JsonResponse
    {
        $validated = $request->validated();

        $result = $login->execute(
            $validated['email'],
            $validated['password'],
            $request->session(),
        );

        return response()->json([
            'data' => [
                'mfa_required' => $result['mfa_required'],
                'mfa_setup_required' => $result['mfa_setup_required'],
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
