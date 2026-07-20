<?php

declare(strict_types=1);

namespace App\Domain\RefundsReturns\Repositories;

use App\Models\Refund;
use App\Models\Sale;
use Illuminate\Support\Collection;

interface RefundsReturnsRepositoryInterface
{
    public function findCompletedSale(int $saleId): ?Sale;

    public function totalRefundedAmount(int $saleId): int;

    /**
     * Quantities already refunded/returned per sale_line_id.
     *
     * @return array<int, int>
     */
    public function refundedQuantitiesBySaleLine(int $saleId): array;

    /**
     * @param array{
     *     sale_id: int,
     *     store_id: int,
     *     user_id: int,
     *     type: string,
     *     reason: string,
     *     amount: string,
     *     payment_refund_reference: string|null,
     * } $header
     * @param list<array{sale_line_id: int, quantity: int, amount: string, restocked: bool}> $lines
     */
    public function create(array $header, array $lines): Refund;

    /**
     * @return Collection<int, Refund>
     */
    public function listForSale(int $saleId): Collection;
}
