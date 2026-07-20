<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Store;
use App\Models\User;
use App\Notifications\Sales\SaleCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListAdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function manager_can_list_database_notifications(): void
    {
        $store = Store::factory()->create();
        $manager = User::factory()->manager()->create();
        $manager->stores()->attach($store);

        $manager->notify(new SaleCompletedNotification(
            saleId: 42,
            storeId: $store->id,
            operatorId: 7,
            totalCents: 1999,
        ));

        $response = $this->actingAs($manager)->getJson('/api/admin/notifications');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.notifications')
            ->assertJsonPath('data.notifications.0.kind', 'sale.completed')
            ->assertJsonPath('data.notifications.0.data.sale_id', 42)
            ->assertJsonPath('data.notifications.0.data.store_id', $store->id)
            ->assertJsonPath('data.notifications.0.data.total', '19.99');
    }

    #[Test]
    public function operator_cannot_list_admin_notifications(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/admin/notifications')
            ->assertForbidden();
    }
}
