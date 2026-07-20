<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Shared\ErrorCode;
use App\Models\User;

final class ShowCurrentUserAction
{
    /**
     * @return array{user: User}
     */
    public function execute(User $user): array
    {
        // Session may hold a stale model; re-read so deactivation takes effect immediately.
        $fresh = $user->fresh();

        if (! $fresh instanceof User) {
            throw new AuthenticationDomainException(ErrorCode::AuthInvalidCredentials);
        }

        if (! $fresh->is_active) {
            throw new AuthenticationDomainException(ErrorCode::AuthAccountInactive);
        }

        return ['user' => $fresh];
    }
}
