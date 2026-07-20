<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Persistence\Repositories;

use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;

final class UsersRepository implements UsersRepositoryInterface
{
    public function list(?string $search = null): Collection
    {
        $query = User::query()
            ->with(['stores:id,name,code'])
            ->orderBy('name')
            ->orderBy('id');

        if ($search !== null && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->get();
    }

    public function listPage(?string $search, ?string $cursor, int $perPage): array
    {
        $query = User::query()
            ->with(['stores:id,name,code'])
            ->orderBy('name')
            ->orderBy('id');

        if ($search !== null && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if ($cursor !== null && $cursor !== '') {
            [$cursorName, $cursorId] = $this->decodeUserCursor($cursor);
            $query->where(function ($builder) use ($cursorName, $cursorId): void {
                $builder
                    ->where('name', '>', $cursorName)
                    ->orWhere(function ($inner) use ($cursorName, $cursorId): void {
                        $inner->where('name', $cursorName)->where('id', '>', $cursorId);
                    });
            });
        }

        /** @var Collection<int, User> $rows */
        $rows = $query->limit($perPage + 1)->get();

        $nextCursor = null;
        if ($rows->count() > $perPage) {
            $rows = $rows->take($perPage)->values();
            $last = $rows->last();
            if ($last !== null) {
                $nextCursor = $this->encodeUserCursor($last);
            }
        }

        return [
            'items' => $rows->values(),
            'next_cursor' => $nextCursor,
        ];
    }

    private function encodeUserCursor(User $user): string
    {
        $payload = json_encode([
            'n' => (string) $user->name,
            'i' => (int) $user->id,
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function decodeUserCursor(string $cursor): array
    {
        $decoded = base64_decode(strtr($cursor, '-_', '+/'), true);
        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid user cursor.');
        }

        try {
            /** @var array{n?: mixed, i?: mixed} $payload */
            $payload = json_decode($decoded, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Invalid user cursor.');
        }

        $name = $payload['n'] ?? null;
        $id = $payload['i'] ?? null;
        if (! is_string($name) || ! is_int($id) || $id < 1) {
            throw new \InvalidArgumentException('Invalid user cursor.');
        }

        return [$name, $id];
    }

    public function findById(int $id): ?User
    {
        return User::query()->with(['stores:id,name,code'])->find($id);
    }

    public function emailExists(string $email, ?int $exceptUserId = null): bool
    {
        $query = User::query()->where('email', $email);

        if ($exceptUserId !== null) {
            $query->whereKeyNot($exceptUserId);
        }

        return $query->exists();
    }

    public function create(array $attributes, array $storeIds): User
    {
        $user = User::query()->create($attributes);
        $user->stores()->sync($storeIds);

        return $user->fresh(['stores:id,name,code']);
    }

    public function update(User $user, array $attributes, ?array $storeIds = null): User
    {
        if ($attributes !== []) {
            $user->update($attributes);
        }

        if ($storeIds !== null) {
            $user->stores()->sync($storeIds);
        }

        return $user->fresh(['stores:id,name,code']);
    }
}
