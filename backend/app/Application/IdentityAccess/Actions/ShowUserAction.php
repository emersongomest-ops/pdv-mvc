<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\User;

final class ShowUserAction
{
    public function __construct(
        private readonly UsersRepositoryInterface $users,
    ) {}

    public function execute(int $userId): User
    {
        $user = $this->users->findById($userId);

        if ($user === null) {
            throw new AuthenticationDomainException(ErrorCode::AuthUserNotFound);
        }

        return $user;
    }
}
