<?php

declare(strict_types=1);

namespace App\Application\Admin\DTOs;

/** Snapshot of independent admin KPI counts (ADR-0006 Concurrency). */
final readonly class AdminDashboardMetrics
{
    public function __construct(
        public int $productsTotal,
        public int $productsActive,
        public int $customersTotal,
        public int $salesCompleted,
        public int $openShifts,
    ) {}

    /**
     * @return array{
     *     products_total: int,
     *     products_active: int,
     *     products_inactive: int,
     *     customers_total: int,
     *     sales_completed: int,
     *     open_shifts: int
     * }
     */
    public function toArray(): array
    {
        return [
            'products_total' => $this->productsTotal,
            'products_active' => $this->productsActive,
            'products_inactive' => max(0, $this->productsTotal - $this->productsActive),
            'customers_total' => $this->customersTotal,
            'sales_completed' => $this->salesCompleted,
            'open_shifts' => $this->openShifts,
        ];
    }
}
