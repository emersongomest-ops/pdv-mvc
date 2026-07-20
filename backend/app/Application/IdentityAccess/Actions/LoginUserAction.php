<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Shared\ErrorCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class LoginUserAction
{
    /**
     * @return array{user: User}
     */
    public function execute(string $email, string $password): array
    {
        $throttleKey = Str::lower($email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw new AuthenticationDomainException(ErrorCode::AuthTooManyAttempts);
        }

        if (! Auth::attempt(['email' => $email, 'password' => $password, 'is_active' => true])) {
            RateLimiter::hit($throttleKey, 60);

            $user = User::query()->where('email', $email)->first();

            if ($user !== null && ! $user->is_active) {
                throw new AuthenticationDomainException(ErrorCode::AuthAccountInactive);
            }

            throw new AuthenticationDomainException(ErrorCode::AuthInvalidCredentials);
        }

        RateLimiter::clear($throttleKey);

        /** @var User $user */
        $user = Auth::user();

        return ['user' => $user];
    }
}
