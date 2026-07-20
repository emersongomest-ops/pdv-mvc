<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Application\Customers\Actions\AnonymizeExpiredCustomersAction;
use Illuminate\Console\Command;

final class AnonymizeExpiredCustomersCommand extends Command
{
    protected $signature = 'customers:anonymize-expired
                            {--days= : Retention days (default from config pii.retention_days)}
                            {--dry-run : Count eligible customers without writing}';

    protected $description = 'Anonymize customer PII past retention window (keep id for sale FKs)';

    public function handle(AnonymizeExpiredCustomersAction $action): int
    {
        $daysOption = $this->option('days');
        $days = is_numeric($daysOption) ? (int) $daysOption : null;
        $dryRun = (bool) $this->option('dry-run');

        $summary = $action->execute($days, $dryRun);

        $verb = $summary['dry_run'] ? 'Would anonymize' : 'Anonymized';

        $this->info(sprintf(
            '%s %d customer(s) older than %d day(s) (cutoff %s).',
            $verb,
            $summary['anonymized'],
            $summary['retention_days'],
            $summary['cutoff'],
        ));

        return self::SUCCESS;
    }
}
