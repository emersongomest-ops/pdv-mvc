<?php

declare(strict_types=1);

namespace App\Http\IdentityAccess\Controllers;

use App\Application\IdentityAccess\Actions\LoginUserAction;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Http\Controllers\Controller;
use App\Http\IdentityAccess\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserAction $login): JsonResponse
    {
        $validated = $request->validated();

        try {
            $result = $login->execute($validated['email'], $validated['password']);
        } catch (AuthenticationDomainException $exception) {
            throw $exception;
        }

        $request->session()->regenerate();

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
