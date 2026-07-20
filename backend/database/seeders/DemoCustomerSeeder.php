<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Models\Store;
use App\Support\Pii\PiiCrypto;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::query()->where('code', 'MAIN')->first();

        if ($store === null) {
            throw new RuntimeException('Demo store MAIN missing — run DemoStoreSeeder first.');
        }

        $maria = $this->findOrCreateCustomer(
            cpf: '39053344705',
            attributes: [
                'name' => 'Maria Silva',
                'email' => 'maria.silva@demo.test',
                'phone' => '11987654321',
                'birth_date' => '1990-03-15',
                'address' => 'Rua das Flores 100, São Paulo - SP',
                'lifetime_spend' => 12500,
            ],
        );

        $joao = $this->findOrCreateCustomer(
            cpf: '52998224725',
            attributes: [
                'name' => 'João Santos',
                'email' => 'joao.santos@demo.test',
                'phone' => '11976543210',
                'birth_date' => '1985-07-22',
                'address' => 'Av. Paulista 1000, São Paulo - SP',
                'lifetime_spend' => 4500,
            ],
        );

        $this->findOrCreateCustomer(
            cpf: '15350946056',
            attributes: [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@demo.test',
                'phone' => '11965432109',
                'birth_date' => '1995-11-08',
                'address' => 'Rua Augusta 500, São Paulo - SP',
                'lifetime_spend' => 0,
            ],
        );

        CustomerStoreStat::query()->firstOrCreate(
            [
                'customer_id' => $maria->id,
                'store_id' => $store->id,
            ],
            [
                'purchase_count' => 4,
                'total_spend' => 12500,
            ],
        );

        CustomerStoreStat::query()->firstOrCreate(
            [
                'customer_id' => $joao->id,
                'store_id' => $store->id,
            ],
            [
                'purchase_count' => 2,
                'total_spend' => 4500,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function findOrCreateCustomer(string $cpf, array $attributes): Customer
    {
        $digits = PiiCrypto::normalizeCpf($cpf);
        $existing = Customer::query()
            ->where('cpf_hash', PiiCrypto::blindIndex($digits))
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return Customer::query()->create([
            ...$attributes,
            'cpf' => $digits,
            // DatabaseSeeder uses WithoutModelEvents — set hashes explicitly.
            'cpf_hash' => PiiCrypto::blindIndex($digits),
            'email_hash' => PiiCrypto::blindIndex(
                'email:'.PiiCrypto::normalizeEmail((string) ($attributes['email'] ?? '')),
            ),
        ]);
    }
}
