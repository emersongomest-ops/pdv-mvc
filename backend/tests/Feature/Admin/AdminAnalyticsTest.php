<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithAdminStoreAccess;
use Tests\TestCase;

final class AdminAnalyticsTest extends TestCase
{
    use ActsWithAdminStoreAccess;
    use RefreshDatabase;

    #[Test]
    public function manager_gets_registrations_recurrence_and_spend_breakdown(): void
    {
        $storeA = Store::factory()->create(['code' => 'A1']);
        $storeB = Store::factory()->create(['code' => 'B1']);
        $manager = User::factory()->manager()->create();
        $this->attachManagerToStore($manager, $storeA);
        $this->attachManagerToStore($manager, $storeB);

        $today = now()->toDateString();
        $repeat = Customer::factory()->create([
            'name' => 'Repeat Buyer',
            'created_at' => now(),
            'lifetime_spend' => 5000,
        ]);
        $once = Customer::factory()->create([
            'name' => 'One Timer',
            'created_at' => now(),
            'lifetime_spend' => 1000,
        ]);
        CustomerStoreStat::query()->create([
            'customer_id' => $repeat->id,
            'store_id' => $storeA->id,
            'purchase_count' => 2,
            'total_spend' => 3000,
        ]);
        CustomerStoreStat::query()->create([
            'customer_id' => $repeat->id,
            'store_id' => $storeB->id,
            'purchase_count' => 1,
            'total_spend' => 2000,
        ]);
        CustomerStoreStat::query()->create([
            'customer_id' => $once->id,
            'store_id' => $storeA->id,
            'purchase_count' => 1,
            'total_spend' => 1000,
        ]);

        $response = $this
            ->actingAs($manager)
            ->getJson('/api/admin/analytics?registration_days=7&top_customers=5');

        $response
            ->assertOk()
            ->assertJsonPath('data.recurrence.customers_with_purchases', 2)
            ->assertJsonPath('data.recurrence.customers_with_repeat', 1)
            ->assertJsonPath('data.recurrence.index', '0.5000')
            ->assertJsonPath('data.top_customers_by_spend.0.customer_id', $repeat->id)
            ->assertJsonPath('data.top_customers_by_spend.0.lifetime_spend', '50.00')
            ->assertJsonPath('data.top_customers_by_spend.0.store_spend.0.store_code', 'A1');

        $dates = collect($response->json('data.registrations_over_time'))->pluck('date');
        $this->assertTrue($dates->contains($today));
        $todayCount = collect($response->json('data.registrations_over_time'))
            ->firstWhere('date', $today)['count'] ?? 0;
        $this->assertGreaterThanOrEqual(2, $todayCount);
    }

    #[Test]
    public function manager_can_filter_campaign_customers_by_birthday_and_region(): void
    {
        $manager = User::factory()->manager()->create();
        Customer::factory()->create([
            'name' => 'July SP',
            'birth_date' => '1990-07-15',
            'address' => 'Rua X, São Paulo - SP',
        ]);
        Customer::factory()->create([
            'name' => 'March RJ',
            'birth_date' => '1988-03-01',
            'address' => 'Av Y, Rio de Janeiro - RJ',
        ]);

        $this->actingAs($manager)
            ->getJson('/api/admin/campaigns/customers?birth_month=7')
            ->assertOk()
            ->assertJsonCount(1, 'data.customers')
            ->assertJsonPath('data.customers.0.name', 'July SP');

        $this->actingAs($manager)
            ->getJson('/api/admin/campaigns/customers?region=rio')
            ->assertOk()
            ->assertJsonCount(1, 'data.customers')
            ->assertJsonPath('data.customers.0.name', 'March RJ');
    }

    #[Test]
    public function operator_cannot_access_analytics(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/admin/analytics')
            ->assertForbidden();

        $this->actingAs($operator)
            ->getJson('/api/admin/campaigns/customers')
            ->assertForbidden();
    }

    #[Test]
    public function invalid_campaign_filters_return_validation_errors(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->getJson('/api/admin/campaigns/customers?birth_month=13')
            ->assertStatus(422);
    }
}
