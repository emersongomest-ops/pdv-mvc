<?php

declare(strict_types=1);

namespace App\Application\Sales\Listeners;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Domain\Sales\Events\SaleCompleted;
use App\Models\User;
use App\Notifications\Sales\SaleCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Notifies active managers assigned to the store where the sale completed.
 */
final class NotifyManagersOfSaleCompleted implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SaleCompleted $event): void
    {
        $managers = User::query()
            ->where('is_active', true)
            ->where('role', UserRole::Manager)
            ->whereHas('stores', fn ($query) => $query->whereKey($event->storeId))
            ->get();

        $notification = new SaleCompletedNotification(
            saleId: $event->saleId,
            storeId: $event->storeId,
            operatorId: $event->operatorId,
            totalCents: $event->totalCents,
        );

        foreach ($managers as $manager) {
            $manager->notify($notification);
        }
    }
}
