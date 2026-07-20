<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

interface UsersRepositoryInterface
{
    /**
     * @return Collection<int, User>
     */
    public function list(?string $search = null): Collection;

    /**
     * Keyset page ordered by name, id.
     *
     * @return array{items: Collection<int, User>, next_cursor: string|null}
     */
    public function listPage(?string $search, ?string $cursor, int $perPage): array;

    public function findById(int $id): ?User;

    public function emailExists(string $email, ?int $exceptUserId = null): bool;

    /**
     * @param  array{name: string, email: string, password: string, role: string, is_active: bool}  $attributes
     * @param  list<int>  $storeIds
     */
    public function create(array $attributes, array $storeIds): User;

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<int>|null  $storeIds
     */
    public function update(User $user, array $attributes, ?array $storeIds = null): User;
}
