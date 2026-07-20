<?php

declare(strict_types=1);

namespace App\Infrastructure\Analytics\Persistence\Repositories;

use App\Domain\Analytics\DTOs\AdminAnalyticsSnapshot;
use App\Domain\Analytics\DTOs\CustomerSpendRow;
use App\Domain\Analytics\DTOs\RegistrationBucket;
use App\Domain\Analytics\Repositories\AnalyticsRepositoryInterface;
use App\Models\Customer;
use App\Models\CustomerStoreStat;
use App\Models\Store;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final class AnalyticsRepository implements AnalyticsRepositoryInterface
{
    public function adminSnapshot(array $storeIds, int $registrationDays = 30, int $topCustomers = 20): AdminAnalyticsSnapshot
    {
        $registrations = $this->registrationsOverTime($registrationDays);
        [$withPurchases, $withRepeat, $index] = $this->recurrence($storeIds);
        $top = $this->topCustomersBySpend($storeIds, $topCustomers);

        return new AdminAnalyticsSnapshot(
            registrationsOverTime: $registrations,
            customersWithPurchases: $withPurchases,
            customersWithRepeatPurchases: $withRepeat,
            recurrenceIndex: $index,
            topCustomersBySpend: $top,
        );
    }

    public function listCampaignCustomers(?int $birthMonth = null, ?string $region = null): Collection
    {
        $query = Customer::query()->orderBy('name');

        if ($birthMonth !== null) {
            $query->whereMonth('birth_date', $birthMonth);
        }

        if ($region !== null && $region !== '') {
            $needle = '%'.mb_strtolower($region).'%';
            $query->whereRaw('LOWER(address) LIKE ?', [$needle]);
        }

        return $query->with('storeStats')->get();
    }

    /**
     * @return list<RegistrationBucket>
     */
    private function registrationsOverTime(int $days): array
    {
        $end = CarbonImmutable::today();
        $start = $end->subDays(max(1, $days) - 1);

        $rows = Customer::query()
            ->whereDate('created_at', '>=', $start->toDateString())
            ->whereDate('created_at', '<=', $end->toDateString())
            ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('aggregate', 'day');

        $buckets = [];
        for ($cursor = $start; $cursor->lte($end); $cursor = $cursor->addDay()) {
            $key = $cursor->toDateString();
            $buckets[] = new RegistrationBucket(
                date: $key,
                count: (int) ($rows[$key] ?? 0),
            );
        }

        return $buckets;
    }

    /**
     * @param  list<int>  $storeIds
     * @return array{0: int, 1: int, 2: float}
     */
    private function recurrence(array $storeIds): array
    {
        if ($storeIds === []) {
            return [0, 0, 0.0];
        }

        $totals = CustomerStoreStat::query()
            ->whereIn('store_id', $storeIds)
            ->selectRaw('customer_id, SUM(purchase_count) as purchases')
            ->groupBy('customer_id')
            ->pluck('purchases');

        $withPurchases = $totals->count();
        $withRepeat = $totals->filter(static fn ($count): bool => (int) $count >= 2)->count();
        $index = $withPurchases === 0 ? 0.0 : round($withRepeat / $withPurchases, 4);

        return [$withPurchases, $withRepeat, $index];
    }

    /**
     * @param  list<int>  $storeIds
     * @return list<CustomerSpendRow>
     */
    private function topCustomersBySpend(array $storeIds, int $limit): array
    {
        if ($storeIds === [] || $limit < 1) {
            return [];
        }

        $customerIds = CustomerStoreStat::query()
            ->whereIn('store_id', $storeIds)
            ->selectRaw('customer_id, SUM(total_spend) as spend')
            ->groupBy('customer_id')
            ->orderByDesc('spend')
            ->limit($limit)
            ->pluck('customer_id');

        if ($customerIds->isEmpty()) {
            return [];
        }

        $customers = Customer::query()
            ->whereIn('id', $customerIds)
            ->get()
            ->keyBy('id');

        $storeCodes = Store::query()
            ->whereIn('id', $storeIds)
            ->pluck('code', 'id');

        $stats = CustomerStoreStat::query()
            ->whereIn('customer_id', $customerIds)
            ->whereIn('store_id', $storeIds)
            ->get()
            ->groupBy('customer_id');

        $rows = [];
        foreach ($customerIds as $customerId) {
            $customer = $customers->get($customerId);
            if ($customer === null) {
                continue;
            }

            $storeSpend = [];
            foreach ($stats->get($customerId, collect()) as $stat) {
                $storeSpend[] = [
                    'store_id' => (int) $stat->store_id,
                    'store_code' => $storeCodes[(int) $stat->store_id] ?? null,
                    'purchase_count' => (int) $stat->purchase_count,
                    'total_spend_cents' => (int) $stat->total_spend,
                ];
            }

            usort(
                $storeSpend,
                static fn (array $a, array $b): int => $b['total_spend_cents'] <=> $a['total_spend_cents'],
            );

            $rows[] = new CustomerSpendRow(
                customerId: (int) $customer->id,
                name: (string) $customer->name,
                cpf: (string) $customer->cpf,
                lifetimeSpendCents: (int) $customer->lifetime_spend,
                storeSpend: $storeSpend,
            );
        }

        return $rows;
    }
}
