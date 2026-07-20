<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\CashShift;
use App\Models\Store;
use App\Models\User;
use App\Support\Store\StoreContext;

trait ActsWithOperationalSession
{
    protected function actingAsOperatorAtStore(User $operator, Store $store): static
    {
        if (! $operator->stores()->where('stores.id', $store->id)->exists()) {
            $operator->stores()->attach($store);
        }

        $this->enableStatefulApiHeaders();

        return $this
            ->actingAs($operator)
            ->withSession([StoreContext::SESSION_KEY => $store->id]);
    }

    protected function withOpenShift(User $operator, Store $store, int $openingCents = 10000): CashShift
    {
        return CashShift::factory()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'opening_cash_amount' => $openingCents,
        ]);
    }

    protected function actingAsOperatorWithOpenShift(User $operator, Store $store, int $openingCents = 10000): static
    {
        $this->withOpenShift($operator, $store, $openingCents);

        return $this->actingAsOperatorAtStore($operator, $store);
    }
}
