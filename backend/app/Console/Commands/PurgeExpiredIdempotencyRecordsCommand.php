<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Shared\Idempotency\PurgeExpiredIdempotencyRecordsAction;
use Illuminate\Console\Command;

final class PurgeExpiredIdempotencyRecordsCommand extends Command
{
    protected $signature = 'idempotency:purge {--days= : Retention days (default from config)}';

    protected $description = 'Delete idempotency_records older than retention window (RN-073)';

    public function handle(PurgeExpiredIdempotencyRecordsAction $purge): int
    {
        $daysOption = $this->option('days');
        $days = is_numeric($daysOption) ? (int) $daysOption : null;

        $summary = $purge->execute($days);

        $this->info(sprintf(
            'Purged %d idempotency record(s) older than %d day(s) (cutoff %s).',
            $summary['deleted'],
            $summary['retention_days'],
            $summary['cutoff'],
        ));

        return self::SUCCESS;
    }
}
