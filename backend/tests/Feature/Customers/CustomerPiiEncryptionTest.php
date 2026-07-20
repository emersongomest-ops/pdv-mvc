<?php

declare(strict_types=1);

namespace Tests\Feature\Customers;

use App\Models\Customer;
use App\Models\Store;
use App\Models\User;
use App\Support\Pii\PiiCrypto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\ActsWithOperationalSession;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class CustomerPiiEncryptionTest extends TestCase
{
    use ActsWithOperationalSession;
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    #[Test]
    public function customer_cpf_is_ciphertext_at_rest_and_hashed_for_lookup(): void
    {
        $customer = Customer::factory()->create([
            'cpf' => '52998224725',
            'email' => 'secure@demo.test',
        ]);

        $row = DB::table('customers')->where('id', $customer->id)->first();

        $this->assertNotSame('52998224725', $row->cpf);
        $this->assertStringNotContainsString('52998224725', (string) $row->cpf);
        $this->assertStringNotContainsString('secure@demo.test', (string) $row->email);
        $this->assertSame(PiiCrypto::blindIndex('52998224725'), $row->cpf_hash);
        $this->assertSame(PiiCrypto::blindIndex('email:secure@demo.test'), $row->email_hash);

        $this->assertSame('52998224725', $customer->fresh()->cpf);
        $this->assertSame('secure@demo.test', $customer->fresh()->email);
    }

    #[Test]
    public function operational_cpf_lookup_returns_masked_cpf(): void
    {
        $store = Store::factory()->create();
        $operator = User::factory()->operator()->create();
        $this->withOpenShift($operator, $store);
        $this->actingAsOperatorAtStore($operator, $store);

        Customer::factory()->create([
            'name' => 'Maria Silva',
            'cpf' => '39053344705',
        ]);

        $this->getJson('/api/operational/customers?cpf=39053344705')
            ->assertOk()
            ->assertJsonPath('data.customer.name', 'Maria Silva')
            ->assertJsonPath('data.customer.cpf', '390.***.***-05');
    }
}
