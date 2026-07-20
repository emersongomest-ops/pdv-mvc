<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\Store;
use App\Models\User;

trait ActsWithAdminStoreAccess
{
    protected function attachManagerToStore(User $manager, Store $store): void
    {
        if (! $manager->stores()->where('stores.id', $store->id)->exists()) {
            $manager->stores()->attach($store);
        }
    }

    protected function actingAsManagerForStore(User $manager, Store $store): static
    {
        $this->attachManagerToStore($manager, $store);

        return $this->actingAs($manager);
    }
}
