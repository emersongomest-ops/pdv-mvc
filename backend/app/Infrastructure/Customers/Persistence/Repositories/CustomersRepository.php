<?php

declare(strict_types=1);

namespace App\Infrastructure\Customers\Persistence\Repositories;

use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\Shared\Money;
use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Support\Pii\PiiCrypto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CustomersRepository implements CustomersRepositoryInterface
{
    public function list(?string $search = null): Collection
    {
        $query = Customer::query()->orderBy('name');

        $this->applySearch($query, $search);

        return $query->get();
    }

    public function listPage(?string $search, ?string $cursor, int $perPage): array
    {
        $query = Customer::query()->orderBy('name')->orderBy('id');

        $this->applySearch($query, $search);

        if ($cursor !== null && $cursor !== '') {
            [$cursorName, $cursorId] = $this->decodeCustomerCursor($cursor);
            $query->where(function ($builder) use ($cursorName, $cursorId): void {
                $builder
                    ->where('name', '>', $cursorName)
                    ->orWhere(function ($inner) use ($cursorName, $cursorId): void {
                        $inner->where('name', $cursorName)->where('id', '>', $cursorId);
                    });
            });
        }

        /** @var Collection<int, Customer> $rows */
        $rows = $query->limit($perPage + 1)->get();

        $nextCursor = null;
        if ($rows->count() > $perPage) {
            $rows = $rows->take($perPage)->values();
            $last = $rows->last();
            if ($last !== null) {
                $nextCursor = $this->encodeCustomerCursor($last);
            }
        }

        return [
            'items' => $rows->values(),
            'next_cursor' => $nextCursor,
        ];
    }

    private function applySearch($query, ?string $search): void
    {
        if ($search === null || $search === '') {
            return;
        }

        $needle = '%'.mb_strtolower($search).'%';
        $cpfDigits = PiiCrypto::normalizeCpf($search);
        $emailNorm = PiiCrypto::normalizeEmail($search);

        $query->where(function ($builder) use ($needle, $cpfDigits, $emailNorm): void {
            $builder->whereRaw('LOWER(name) LIKE ?', [$needle]);

            if (strlen($cpfDigits) === 11) {
                $builder->orWhere('cpf_hash', PiiCrypto::blindIndex($cpfDigits));
            }

            if (str_contains($emailNorm, '@')) {
                $builder->orWhere('email_hash', PiiCrypto::blindIndex('email:'.$emailNorm));
            }
        });
    }

    private function encodeCustomerCursor(Customer $customer): string
    {
        $payload = json_encode([
            'n' => (string) $customer->name,
            'i' => (int) $customer->id,
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function decodeCustomerCursor(string $cursor): array
    {
        $decoded = base64_decode(strtr($cursor, '-_', '+/'), true);
        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid customer cursor.');
        }

        try {
            /** @var array{n?: mixed, i?: mixed} $payload */
            $payload = json_decode($decoded, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Invalid customer cursor.');
        }

        $name = $payload['n'] ?? null;
        $id = $payload['i'] ?? null;
        if (! is_string($name) || ! is_int($id) || $id < 1) {
            throw new \InvalidArgumentException('Invalid customer cursor.');
        }

        return [$name, $id];
    }

    public function findById(int $id): ?Customer
    {
        return Customer::query()->with('storeStats')->find($id);
    }

    public function findByCpf(string $cpf): ?Customer
    {
        $digits = PiiCrypto::normalizeCpf($cpf);
        if ($digits === '') {
            return null;
        }

        return Customer::query()
            ->where('cpf_hash', PiiCrypto::blindIndex($digits))
            ->first();
    }

    public function cpfExists(string $cpf, ?int $exceptCustomerId = null): bool
    {
        $digits = PiiCrypto::normalizeCpf($cpf);
        if ($digits === '') {
            return false;
        }

        $query = Customer::query()->where('cpf_hash', PiiCrypto::blindIndex($digits));

        if ($exceptCustomerId !== null) {
            $query->whereKeyNot($exceptCustomerId);
        }

        return $query->exists();
    }

    public function create(array $data): Customer
    {
        return Customer::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'],
            'phone' => $data['phone'],
            'birth_date' => $data['birth_date'],
            'address' => $data['address'],
            'lifetime_spend' => 0,
        ]);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh(['storeStats']) ?? $customer;
    }

    public function recordCompletedPurchase(int $customerId, int $storeId, int $amountCents): void
    {
        DB::transaction(function () use ($customerId, $storeId, $amountCents): void {
            $customer = Customer::query()->whereKey($customerId)->lockForUpdate()->first();

            if ($customer === null) {
                return;
            }

            $customer->update([
                'lifetime_spend' => Money::add((int) $customer->lifetime_spend, $amountCents),
            ]);

            $stat = CustomerStoreStat::query()
                ->where('customer_id', $customerId)
                ->where('store_id', $storeId)
                ->lockForUpdate()
                ->first();

            if ($stat === null) {
                CustomerStoreStat::query()->create([
                    'customer_id' => $customerId,
                    'store_id' => $storeId,
                    'purchase_count' => 1,
                    'total_spend' => $amountCents,
                ]);

                return;
            }

            $stat->update([
                'purchase_count' => $stat->purchase_count + 1,
                'total_spend' => Money::add((int) $stat->total_spend, $amountCents),
            ]);
        });
    }
}
