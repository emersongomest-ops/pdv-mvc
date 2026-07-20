<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Domain\Shared\ErrorCode;
use App\Models\User;

final class CreateUserAction
{
    public function __construct(
        private readonly UsersRepositoryInterface $users,
    ) {}

    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     password: string,
     *     role: string,
     *     is_active?: bool,
     *     store_ids: list<int>
     * } $data
     */
    public function execute(array $data): User
    {
        $email = strtolower(trim($data['email']));

        if ($this->users->emailExists($email)) {
            throw new AuthenticationDomainException(ErrorCode::AuthEmailDuplicate);
        }

        /** @var list<int> $storeIds */
        $storeIds = array_values(array_map(static fn ($id): int => (int) $id, $data['store_ids']));

        return $this->users->create([
            'name' => trim($data['name']),
            'email' => $email,
            'password' => $data['password'],
            'role' => UserRole::from($data['role'])->value,
            'is_active' => $data['is_active'] ?? true,
        ], $storeIds);
    }
}
