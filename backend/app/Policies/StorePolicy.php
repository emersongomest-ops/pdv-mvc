<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

final class StorePolicy
{
    public function access(User $user, Store $store): bool
    {
        if (! $store->is_active) {
            return false;
        }

        return $user->stores()->where('stores.id', $store->id)->exists();
    }
}
