<?php

declare(strict_types=1);

namespace Tests\Feature\Sales;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Notifications\Sales\SaleCompletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class SaleCompletedNotificationTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function complete_sale_notifies_active_managers_on_the_store(): void
    {
        $store = Store::factory()->create();
        $otherStore = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $manager = User::factory()->manager()->create();
        $manager->stores()->attach($store);
        $inactiveManager = User::factory()->manager()->inactive()->create();
        $inactiveManager->stores()->attach($store);
        $otherStoreManager = User::factory()->manager()->create();
        $otherStoreManager->stores()->attach($otherStore);

        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $manager->id,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $inactiveManager->id,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $otherStoreManager->id,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $operator->id,
        ]);

        $notification = $manager->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('sale.completed', $notification->data['kind']);
        $this->assertSame($saleId, $notification->data['sale_id']);
        $this->assertSame($store->id, $notification->data['store_id']);
        $this->assertSame($operator->id, $notification->data['operator_id']);
        $this->assertSame('10.00', $notification->data['total']);
    }

    #[Test]
    public function sale_completed_notification_uses_database_and_broadcast_channels(): void
    {
        $manager = User::factory()->manager()->create();
        $notification = new SaleCompletedNotification(
            saleId: 1,
            storeId: 2,
            operatorId: 3,
            totalCents: 1500,
        );

        $this->assertSame(['database', 'broadcast'], $notification->via($manager));
    }

    #[Test]
    public function complete_sale_broadcasts_notification_to_store_manager(): void
    {
        Event::fake([BroadcastNotificationCreated::class]);

        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $manager = User::factory()->manager()->create();
        $manager->stores()->attach($store);

        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 1000]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 10.00],
            ],
        ])->assertOk();

        Event::assertDispatched(
            BroadcastNotificationCreated::class,
            fn (BroadcastNotificationCreated $event): bool => $event->notifiable->is($manager)
                && $event->data['kind'] === 'sale.completed'
                && $event->data['sale_id'] === $saleId,
        );
    }

    #[Test]
    public function complete_sale_queues_manager_notification_listener(): void
    {
        Notification::fake();

        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $manager = User::factory()->manager()->create();
        $manager->stores()->attach($store);

        $this->actingAsOperatorWithOpenShift($operator, $store);

        $product = Product::factory()->create(['base_price' => 500]);
        $saleId = (int) $this->postJson('/api/operational/sales')->json('data.sale.id');

        $this->postJson("/api/operational/sales/{$saleId}/lines", [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->postJson("/api/operational/sales/{$saleId}/complete", [
            'payments' => [
                ['method' => 'pix', 'amount' => 5.00],
            ],
        ])->assertOk();

        Notification::assertSentTo(
            $manager,
            SaleCompletedNotification::class,
            fn (SaleCompletedNotification $notification): bool => true,
        );
    }
}
