<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use App\Models\IdempotencyRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class PurgeExpiredIdempotencyRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_deletes_only_rows_older_than_retention(): void
    {
        $user = User::factory()->operator()->create();

        $stale = IdempotencyRecord::query()->create([
            'key' => 'stale-key-1',
            'scope' => 'sales.complete:1',
            'user_id' => $user->id,
            'request_hash' => str_repeat('a', 64),
            'request_id' => null,
            'status' => IdempotencyRecord::STATUS_COMPLETED,
            'response_code' => 200,
            'response_body' => ['ok' => true],
        ]);
        $stale->forceFill(['created_at' => now()->subDays(8)])->save();

        $fresh = IdempotencyRecord::query()->create([
            'key' => 'fresh-key-1',
            'scope' => 'sales.complete:2',
            'user_id' => $user->id,
            'request_hash' => str_repeat('b', 64),
            'request_id' => null,
            'status' => IdempotencyRecord::STATUS_COMPLETED,
            'response_code' => 200,
            'response_body' => ['ok' => true],
        ]);
        $fresh->forceFill(['created_at' => now()->subDays(2)])->save();

        $this->artisan('idempotency:purge')
            ->expectsOutputToContain('Purged 1 idempotency record')
            ->assertSuccessful();

        $this->assertDatabaseMissing('idempotency_records', ['id' => $stale->id]);
        $this->assertDatabaseHas('idempotency_records', ['id' => $fresh->id]);
    }

    public function test_purge_respects_days_option(): void
    {
        $user = User::factory()->operator()->create();

        $row = IdempotencyRecord::query()->create([
            'key' => 'mid-key',
            'scope' => 'sales.refund:9',
            'user_id' => $user->id,
            'request_hash' => str_repeat('c', 64),
            'status' => IdempotencyRecord::STATUS_PROCESSING,
        ]);
        $row->forceFill(['created_at' => now()->subDays(3)])->save();

        Artisan::call('idempotency:purge', ['--days' => 2]);

        $this->assertDatabaseMissing('idempotency_records', ['id' => $row->id]);
    }
}
