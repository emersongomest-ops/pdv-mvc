<?php

declare(strict_types=1);

namespace App\Notifications\Sales;

use App\Domain\Shared\Money;
use Illuminate\Notifications\Notification;

final class SaleCompletedNotification extends Notification
{
    public function __construct(
        private readonly int $saleId,
        private readonly int $storeId,
        private readonly int $operatorId,
        private readonly int $totalCents,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'sale.completed',
            'sale_id' => $this->saleId,
            'store_id' => $this->storeId,
            'operator_id' => $this->operatorId,
            'total' => Money::toDecimalString($this->totalCents),
            'message' => "Sale #{$this->saleId} completed.",
        ];
    }
}
