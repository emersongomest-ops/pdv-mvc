<?php

declare(strict_types=1);

namespace Tests\Feature\Promotions;

use App\Models\Customer;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PromotionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_promotion_for_all_customers(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->postJson('/api/admin/promotions', [
            'code' => 'SUMMER10',
            'name' => 'Summer 10%',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'stacking_mode' => 'accumulable',
            'applies_to_all_customers' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.promotion.code', 'SUMMER10')
            ->assertJsonPath('data.promotion.stacking_mode', 'accumulable')
            ->assertJsonPath('data.promotion.applies_to_all_customers', true);

        $this->assertDatabaseHas('promotions', [
            'code' => 'SUMMER10',
            'discount_type' => 'percent',
        ]);
    }

    public function test_manager_can_create_promotion_assigned_to_customers(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($manager)->postJson('/api/admin/promotions', [
            'code' => 'VIP20',
            'name' => 'VIP 20%',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'stacking_mode' => 'unique',
            'applies_to_all_customers' => false,
            'customer_ids' => [$customer->id],
        ])
            ->assertCreated()
            ->assertJsonPath('data.promotion.customer_ids.0', $customer->id);

        $this->assertDatabaseHas('customer_promotion', [
            'customer_id' => $customer->id,
        ]);
    }

    public function test_manager_can_list_and_update_promotion(): void
    {
        $manager = User::factory()->manager()->create();
        $promotion = Promotion::factory()->create(['code' => 'OLD10', 'is_active' => true]);

        $this->actingAs($manager)
            ->getJson('/api/admin/promotions')
            ->assertOk()
            ->assertJsonCount(1, 'data.promotions');

        $this->actingAs($manager)->patchJson("/api/admin/promotions/{$promotion->id}", [
            'is_active' => false,
        ])
            ->assertOk()
            ->assertJsonPath('data.promotion.is_active', false);
    }

    public function test_manager_can_paginate_promotions_with_cursor(): void
    {
        $manager = User::factory()->manager()->create();

        Promotion::factory()->create(['code' => 'AAA10']);
        Promotion::factory()->create(['code' => 'BBB20']);
        Promotion::factory()->create(['code' => 'CCC30']);

        $first = $this->actingAs($manager)->getJson('/api/admin/promotions?per_page=2');
        $first
            ->assertOk()
            ->assertJsonCount(2, 'data.promotions')
            ->assertJsonPath('data.promotions.0.code', 'AAA10')
            ->assertJsonPath('data.promotions.1.code', 'BBB20');

        $cursor = $first->json('meta.next_cursor');
        $this->assertNotNull($cursor);

        $second = $this->actingAs($manager)->getJson(
            '/api/admin/promotions?per_page=2&cursor='.urlencode((string) $cursor),
        );
        $second
            ->assertOk()
            ->assertJsonCount(1, 'data.promotions')
            ->assertJsonPath('data.promotions.0.code', 'CCC30')
            ->assertJsonPath('meta.next_cursor', null);
    }

    public function test_invalid_promotions_cursor_returns_validation_error(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/promotions?per_page=2&cursor=not-a-cursor')
            ->assertStatus(422);
    }

    public function test_operator_cannot_manage_promotions(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/admin/promotions')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'AUTH_ADMIN_ONLY');
    }
}
