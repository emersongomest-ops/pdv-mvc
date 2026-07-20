<?php

declare(strict_types=1);

namespace Tests\Feature\Store;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class ListStoresTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    public function test_guest_cannot_list_stores(): void
    {
        $response = $this->getJson('/api/stores');

        $response
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_UNAUTHENTICATED');
    }

    public function test_operator_sees_only_assigned_stores(): void
    {
        $assigned = Store::factory()->create(['name' => 'Downtown']);
        $other = Store::factory()->create(['name' => 'Airport']);
        $operator = User::factory()->operator()->create();
        $operator->stores()->attach($assigned);

        $response = $this->actingAs($operator)->getJson('/api/stores');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.stores')
            ->assertJsonPath('data.stores.0.id', $assigned->id)
            ->assertJsonPath('data.stores.0.name', 'Downtown');

        $ids = collect($response->json('data.stores'))->pluck('id')->all();
        $this->assertNotContains($other->id, $ids);
    }

    public function test_manager_sees_all_assigned_stores(): void
    {
        $first = Store::factory()->create(['name' => 'North']);
        $second = Store::factory()->create(['name' => 'South']);
        $manager = User::factory()->manager()->create();
        $manager->stores()->attach([$first->id, $second->id]);

        $response = $this->actingAs($manager)->getJson('/api/stores');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.stores');
    }
}
