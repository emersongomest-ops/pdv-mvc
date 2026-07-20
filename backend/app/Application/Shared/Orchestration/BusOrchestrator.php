<?php

declare(strict_types=1);

namespace App\Application\Shared\Orchestration;

use Illuminate\Bus\Batch;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;
use Throwable;

/**
 * Thin Laravel Bus facade for hybrid queue orchestration (ADR-0006).
 *
 * Application Actions call this after the transactional critical path — never
 * from Domain. Prefer {@see chain} for ordered steps, {@see batch} for
 * independent parallel jobs, {@see hybrid} when stages mix both.
 */
final class BusOrchestrator
{
    /**
     * Sequential jobs: each runs only if the previous succeeded.
     *
     * @param  list<object>  $jobs
     */
    public function chain(array $jobs, ?callable $catch = null): PendingChain
    {
        $pending = Bus::chain($jobs);

        if ($catch !== null) {
            $pending->catch($catch);
        }

        return $pending;
    }

    /**
     * Independent jobs that may run in parallel on the queue workers.
     *
     * @param  list<object>  $jobs
     */
    public function batch(array $jobs, string $name = ''): PendingBatch
    {
        $pending = Bus::batch($jobs);

        if ($name !== '') {
            $pending->name($name);
        }

        return $pending;
    }

    /**
     * Ordered stages where a stage is either one job or a parallel batch of jobs.
     *
     * Example (Laravel batches-in-chains):
     * `hybrid([ $jobA, [$jobB1, $jobB2], $jobC ])`
     * → chain: A → batch(B1,B2) → C
     *
     * @param  list<object|list<object>>  $stages
     */
    public function hybrid(array $stages, ?callable $catch = null): PendingChain
    {
        $links = [];

        foreach ($stages as $stage) {
            if (is_array($stage)) {
                $links[] = Bus::batch(array_values($stage));

                continue;
            }

            $links[] = $stage;
        }

        return $this->chain($links, $catch);
    }

    /**
     * @param  list<object>  $jobs
     */
    public function dispatchBatch(
        array $jobs,
        string $name = '',
        ?callable $then = null,
        ?callable $catch = null,
        ?callable $finally = null,
    ): Batch {
        $pending = $this->batch($jobs, $name);

        if ($then !== null) {
            $pending->then($then);
        }

        if ($catch !== null) {
            /** @var callable(Batch, Throwable): void $catch */
            $pending->catch($catch);
        }

        if ($finally !== null) {
            $pending->finally($finally);
        }

        return $pending->dispatch();
    }
}
