<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Models\Store;
use App\Models\User;

final class UserResource
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(User $user): array
    {
        $user->loadMissing('stores');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'is_active' => $user->is_active,
            'mfa_enabled' => $user->hasMfaEnabled(),
            'stores' => $user->stores
                ->map(static fn (Store $store): array => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'code' => $store->code,
                ])
                ->values()
                ->all(),
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }
}
