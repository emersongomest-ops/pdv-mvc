<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Shared\Orchestration;

use App\Application\Shared\Orchestration\BusOrchestrator;
use App\Jobs\AbstractQueuedJob;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class BusOrchestratorTest extends TestCase
{
    #[Test]
    public function chain_builds_pending_chain_without_dispatching(): void
    {
        Bus::fake();

        $orchestrator = new BusOrchestrator;
        $pending = $orchestrator->chain([
            new ProbeQueuedJob('a'),
            new ProbeQueuedJob('b'),
        ]);

        $this->assertInstanceOf(PendingChain::class, $pending);
        Bus::assertNothingDispatched();
    }

    #[Test]
    public function batch_builds_named_pending_batch(): void
    {
        Bus::fake();

        $orchestrator = new BusOrchestrator;
        $pending = $orchestrator->batch([
            new ProbeQueuedJob('x'),
            new ProbeQueuedJob('y'),
        ], 'probe-batch');

        $this->assertInstanceOf(PendingBatch::class, $pending);
        $this->assertSame('probe-batch', $pending->name);
        Bus::assertNothingDispatched();
    }

    #[Test]
    public function hybrid_wraps_array_stages_as_batches_inside_chain(): void
    {
        Bus::fake();

        $orchestrator = new BusOrchestrator;
        $pending = $orchestrator->hybrid([
            new ProbeQueuedJob('first'),
            [new ProbeQueuedJob('parallel-1'), new ProbeQueuedJob('parallel-2')],
            new ProbeQueuedJob('last'),
        ]);

        $this->assertInstanceOf(PendingChain::class, $pending);
        Bus::assertNothingDispatched();
    }

    #[Test]
    public function dispatch_batch_pushes_jobs_onto_the_bus(): void
    {
        Bus::fake();

        $orchestrator = new BusOrchestrator;
        $batch = $orchestrator->dispatchBatch([
            new ProbeQueuedJob('one'),
            new ProbeQueuedJob('two'),
        ], 'dispatched-probe');

        $this->assertSame('dispatched-probe', $batch->name);
        Bus::assertBatched(function (PendingBatch $pending): bool {
            return $pending->name === 'dispatched-probe'
                && $pending->jobs->count() === 2;
        });
    }

    #[Test]
    public function abstract_queued_job_is_queueable(): void
    {
        $job = new ProbeQueuedJob('probe');

        $this->assertInstanceOf(AbstractQueuedJob::class, $job);
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $job);
    }
}

/** Probe job for BusOrchestrator unit tests only. */
final class ProbeQueuedJob extends AbstractQueuedJob
{
    public function __construct(public readonly string $label) {}

    public function handle(): void
    {
        // no-op
    }
}
