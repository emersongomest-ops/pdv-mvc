<?php

declare(strict_types=1);

namespace App\Domain\Promotions\Repositories;

use App\Models\Promotion;
use App\Models\SalePromotion;
use Illuminate\Support\Collection;

interface PromotionsRepositoryInterface
{
    /**
     * @return Collection<int, Promotion>
     */
    public function list(): Collection;

    /**
     * Keyset page ordered by code, id.
     *
     * @return array{items: Collection<int, Promotion>, next_cursor: string|null}
     */
    public function listPage(?string $cursor, int $perPage): array;

    public function findById(int $id): ?Promotion;

    public function findByCode(string $code): ?Promotion;

    public function codeExists(string $code, ?int $exceptPromotionId = null): bool;

    /**
     * @param array{
     *     code: string,
     *     name: string,
     *     discount_type: string,
     *     discount_value: int,
     *     stacking_mode: string,
     *     applies_to_all_customers: bool,
     *     is_active: bool,
     *     starts_at?: string|null,
     *     ends_at?: string|null,
     * } $data
     * @param  list<int>  $customerIds
     */
    public function create(array $data, array $customerIds = []): Promotion;

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>|null  $customerIds
     */
    public function update(Promotion $promotion, array $data, ?array $customerIds = null): Promotion;

    public function isAssignedToCustomer(int $promotionId, int $customerId): bool;

    /**
     * @return Collection<int, SalePromotion>
     */
    public function listAppliedForSale(int $saleId): Collection;

    public function findApplied(int $saleId, int $promotionId): ?SalePromotion;

    public function attachToSale(int $saleId, int $promotionId, int $discountAmountCents): SalePromotion;

    public function detachFromSale(int $saleId, int $promotionId): void;

    public function updateAppliedAmount(SalePromotion $applied, int $discountAmountCents): void;
}
