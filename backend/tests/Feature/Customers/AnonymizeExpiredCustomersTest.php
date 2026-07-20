<?php

declare(strict_types=1);

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AnonymizeExpiredCustomersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function anonymizes_customers_with_stale_last_sale(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();

        $stale = Customer::factory()->create([
            'name' => 'Stale Person',
            'email' => 'stale@example.com',
            'cpf' => '12345678901',
        ]);
        Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'customer_id' => $stale->id,
            'completed_at' => now()->subDays(40),
        ]);

        $fresh = Customer::factory()->create([
            'name' => 'Fresh Person',
            'email' => 'fresh@example.com',
            'cpf' => '98765432100',
        ]);
        Sale::factory()->completed()->create([
            'store_id' => $store->id,
            'user_id' => $operator->id,
            'customer_id' => $fresh->id,
            'completed_at' => now()->subDays(5),
        ]);

        $this->artisan('customers:anonymize-expired', ['--days' => 30])
            ->expectsOutputToContain('Anonymized 1 customer')
            ->assertSuccessful();

        $stale->refresh();
        $fresh->refresh();

        $this->assertNotNull($stale->anonymized_at);
        $this->assertSame('Anonymized #'.$stale->id, $stale->name);
        $this->assertSame('anon.'.$stale->id.'@anonymized.invalid', $stale->email);
        $this->assertSame('REDACTED', $stale->address);

        $this->assertNull($fresh->anonymized_at);
        $this->assertSame('Fresh Person', $fresh->name);
    }

    #[Test]
    public function anonymizes_customers_never_sold_when_created_before_cutoff(): void
    {
        $neverSold = Customer::factory()->create([
            'name' => 'Never Bought',
            'cpf' => '11122233344',
        ]);
        $neverSold->forceFill(['created_at' => now()->subDays(100), 'updated_at' => now()->subDays(100)])->save();

        $this->artisan('customers:anonymize-expired', ['--days' => 30])
            ->expectsOutputToContain('Anonymized 1 customer')
            ->assertSuccessful();

        $neverSold->refresh();
        $this->assertNotNull($neverSold->anonymized_at);
        $this->assertSame('Anonymized #'.$neverSold->id, $neverSold->name);
    }

    #[Test]
    public function dry_run_does_not_write(): void
    {
        $customer = Customer::factory()->create(['cpf' => '55566677788']);
        $customer->forceFill(['created_at' => now()->subDays(100)])->save();

        $this->artisan('customers:anonymize-expired', ['--days' => 30, '--dry-run' => true])
            ->expectsOutputToContain('Would anonymize 1 customer')
            ->assertSuccessful();

        $customer->refresh();
        $this->assertNull($customer->anonymized_at);
        $this->assertNotSame('Anonymized #'.$customer->id, $customer->name);
    }

    #[Test]
    public function already_anonymized_customers_are_skipped(): void
    {
        $customer = Customer::factory()->create(['cpf' => '44433322211']);
        $customer->forceFill([
            'created_at' => now()->subDays(100),
            'anonymized_at' => now()->subDay(),
            'name' => 'Anonymized #'.$customer->id,
        ])->save();

        $this->artisan('customers:anonymize-expired', ['--days' => 30])
            ->expectsOutputToContain('Anonymized 0 customer')
            ->assertSuccessful();
    }
}
