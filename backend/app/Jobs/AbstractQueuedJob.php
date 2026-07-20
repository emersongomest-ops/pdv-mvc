<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Base for async work after the Action critical path (ADR-0006).
 *
 * Domain stays free of queue facades; Actions dispatch subclasses of this job.
 *
 * Includes {@see Batchable} so jobs may join `Bus::batch` / `BusOrchestrator::batch`.
 * Prefer dispatching **after** `DB::transaction` returns. Call `$this->afterCommit()`
 * only when dispatching from inside an open transaction (PHPUnit `RefreshDatabase`
 * wraps tests in a TX and would defer after-commit jobs past assertions).
 */
abstract class AbstractQueuedJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return [static::class];
    }
}
