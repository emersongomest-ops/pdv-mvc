<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Payments\Actions\ReconcilePendingPaymentsAction;
use Illuminate\Console\Command;

final class ReconcilePendingPaymentsCommand extends Command
{
    protected $signature = 'payments:reconcile {--store= : Optional store id scope}';

    protected $description = 'Retry queued webhooks and poll pending payment settlements (hourly fallback)';

    public function handle(ReconcilePendingPaymentsAction $reconcile): int
    {
        $storeOption = $this->option('store');
        $storeId = is_numeric($storeOption) ? (int) $storeOption : null;

        $summary = $reconcile->execute($storeId);

        $this->table(array_keys($summary), [array_values($summary)]);

        return self::SUCCESS;
    }
}
