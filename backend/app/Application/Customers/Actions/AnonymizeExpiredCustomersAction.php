<?php

declare(strict_types=1);

namespace App\Application\Customers\Actions;

use App\Models\Customer;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Anonymize customer PII past retention (docs/legal/data-retention.md / RN-072).
 * Keeps row id for historical sale FKs; clears searchable blind indexes via new sentinel values.
 */
final class AnonymizeExpiredCustomersAction
{
    /**
     * @return array{anonymized: int, retention_days: int, cutoff: string, dry_run: bool}
     */
    public function execute(?int $retentionDays = null, bool $dryRun = false): array
    {
        $days = $retentionDays ?? (int) config('pii.retention_days', 1825);
        if ($days < 1) {
            $days = 1825;
        }

        $cutoff = CarbonImmutable::now()->subDays($days);
        $count = 0;

        $query = Customer::query()
            ->whereNull('anonymized_at')
            ->where(function ($builder) use ($cutoff): void {
                $builder
                    ->where(function ($neverSold) use ($cutoff): void {
                        $neverSold
                            ->whereDoesntHave('sales')
                            ->where('customers.created_at', '<', $cutoff);
                    })
                    ->orWhere(function ($sold) use ($cutoff): void {
                        $sold
                            ->whereHas('sales')
                            ->whereRaw(
                                '(select max(coalesce(completed_at, created_at)) from sales where sales.customer_id = customers.id) < ?',
                                [$cutoff->toDateTimeString()],
                            );
                    });
            })
            ->orderBy('id');

        $query->chunkById(100, function ($customers) use (&$count, $dryRun): void {
            /** @var Customer $customer */
            foreach ($customers as $customer) {
                if ($dryRun) {
                    $count++;
                    continue;
                }

                DB::transaction(function () use ($customer, &$count): void {
                    $this->anonymize($customer);
                    $count++;
                });
            }
        });

        return [
            'anonymized' => $count,
            'retention_days' => $days,
            'cutoff' => $cutoff->toIso8601String(),
            'dry_run' => $dryRun,
        ];
    }

    private function anonymize(Customer $customer): void
    {
        $id = (int) $customer->id;

        $customer->forceFill([
            'name' => 'Anonymized #'.$id,
            'email' => 'anon.'.$id.'@anonymized.invalid',
            'cpf' => sprintf('%011d', $id % 100_000_000_000),
            'phone' => '00000000000',
            'birth_date' => '1900-01-01',
            'address' => 'REDACTED',
            'anonymized_at' => CarbonImmutable::now(),
        ]);

        $customer->save();
    }
}
