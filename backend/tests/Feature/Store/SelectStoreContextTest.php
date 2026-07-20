<?php

declare(strict_types=1);

namespace Tests\Feature\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class SelectStoreContextTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    public function test_operator_can_select_assigned_store_context(): void
    {
        $store = Store::factory()->create(['name' => 'Main Branch']);
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($store);

        $response = $this->actingAs($operator)->postJson('/api/stores/context', [
            'store_id' => $store->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.store.id', $store->id)
            ->assertJsonPath('data.store.name', 'Main Branch')
            ->assertJsonPath('data.message', 'Store context selected.');
    }

    public function test_operator_cannot_select_unassigned_store(): void
    {
        $assigned = Store::factory()->create();
        $unassigned = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($assigned);

        $response = $this->actingAs($operator)->postJson('/api/stores/context', [
            'store_id' => $unassigned->id,
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_STORE_ACCESS_DENIED');
    }

    public function test_selecting_nonexistent_store_returns_store_not_found(): void
    {
        $operator = User::factory()->operator()->create();

        $response = $this->actingAs($operator)->postJson('/api/stores/context', [
            'store_id' => 99999,
        ]);

        $response
            ->assertNotFound()
            ->assertJsonPath('error.code', 'STORE_NOT_FOUND');
    }

    public function test_selecting_inactive_store_returns_store_inactive(): void
    {
        $store = Store::factory()->inactive()->create();
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($store);

        $response = $this->actingAs($operator)->postJson('/api/stores/context', [
            'store_id' => $store->id,
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('error.code', 'STORE_INACTIVE');
    }

    public function test_manager_can_select_any_assigned_store(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $manager->stores()->attach($store);

        $this->actingAs($manager)->postJson('/api/stores/context', [
            'store_id' => $store->id,
        ])->assertOk();
    }
}
