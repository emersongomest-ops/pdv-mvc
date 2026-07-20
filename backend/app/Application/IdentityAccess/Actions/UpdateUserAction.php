<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Domain\Shared\ErrorCode;
use App\Models\User;

final class UpdateUserAction
{
    public function __construct(
        private readonly UsersRepositoryInterface $users,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function execute(int $userId, array $data, int $actorUserId): User
    {
        $user = $this->users->findById($userId);

        if ($user === null) {
            throw new AuthenticationDomainException(ErrorCode::AuthUserNotFound);
        }

        $updates = [];

        if (array_key_exists('name', $data)) {
            $updates['name'] = trim((string) $data['name']);
        }

        if (array_key_exists('email', $data)) {
            $email = strtolower(trim((string) $data['email']));
            if ($this->users->emailExists($email, $userId)) {
                throw new AuthenticationDomainException(ErrorCode::AuthEmailDuplicate);
            }
            $updates['email'] = $email;
        }

        if (array_key_exists('password', $data) && $data['password'] !== null && $data['password'] !== '') {
            $updates['password'] = (string) $data['password'];
        }

        if (array_key_exists('role', $data)) {
            $role = UserRole::from((string) $data['role']);
            if ($userId === $actorUserId && $role !== UserRole::Manager) {
                throw new AuthenticationDomainException(ErrorCode::AuthCannotModifySelf);
            }
            $updates['role'] = $role->value;
        }

        if (array_key_exists('is_active', $data)) {
            $isActive = (bool) $data['is_active'];
            if ($userId === $actorUserId && $isActive === false) {
                throw new AuthenticationDomainException(ErrorCode::AuthCannotModifySelf);
            }
            $updates['is_active'] = $isActive;
        }

        $storeIds = null;
        if (array_key_exists('store_ids', $data)) {
            /** @var list<int> $storeIds */
            $storeIds = array_values(array_map(static fn ($id): int => (int) $id, $data['store_ids']));
        }

        return $this->users->update($user, $updates, $storeIds);
    }
}
