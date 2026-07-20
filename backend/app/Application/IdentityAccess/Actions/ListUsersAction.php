<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class ListUsersAction
{
    public function __construct(
        private readonly UsersRepositoryInterface $users,
    ) {}

    /**
     * Without per_page: full list. With per_page: keyset page.
     *
     * @return array{users: Collection<int, User>, next_cursor: string|null}
     */
    public function execute(
        ?string $search = null,
        ?string $cursor = null,
        ?int $perPage = null,
    ): array {
        if ($perPage === null) {
            return [
                'users' => $this->users->list($search),
                'next_cursor' => null,
            ];
        }

        try {
            $page = $this->users->listPage($search, $cursor, $perPage);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        return [
            'users' => $page['items'],
            'next_cursor' => $page['next_cursor'],
        ];
    }
}
