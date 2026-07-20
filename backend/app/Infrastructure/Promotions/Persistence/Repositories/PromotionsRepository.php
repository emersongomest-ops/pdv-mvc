<?php

declare(strict_types=1);

namespace App\Infrastructure\Promotions\Persistence\Repositories;

use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Models\Promotion;
use App\Models\SalePromotion;
use Illuminate\Support\Collection;

final class PromotionsRepository implements PromotionsRepositoryInterface
{
    public function list(): Collection
    {
        return Promotion::query()->with('customers')->orderBy('code')->orderBy('id')->get();
    }

    public function listPage(?string $cursor, int $perPage): array
    {
        $query = Promotion::query()
            ->with('customers')
            ->orderBy('code')
            ->orderBy('id');

        if ($cursor !== null && $cursor !== '') {
            [$cursorCode, $cursorId] = $this->decodePromotionCursor($cursor);
            $query->where(function ($builder) use ($cursorCode, $cursorId): void {
                $builder
                    ->where('code', '>', $cursorCode)
                    ->orWhere(function ($inner) use ($cursorCode, $cursorId): void {
                        $inner->where('code', $cursorCode)->where('id', '>', $cursorId);
                    });
            });
        }

        /** @var Collection<int, Promotion> $rows */
        $rows = $query->limit($perPage + 1)->get();

        $nextCursor = null;
        if ($rows->count() > $perPage) {
            $rows = $rows->take($perPage)->values();
            $last = $rows->last();
            if ($last !== null) {
                $nextCursor = $this->encodePromotionCursor($last);
            }
        }

        return [
            'items' => $rows->values(),
            'next_cursor' => $nextCursor,
        ];
    }

    private function encodePromotionCursor(Promotion $promotion): string
    {
        $payload = json_encode([
            'c' => (string) $promotion->code,
            'i' => (int) $promotion->id,
        ], JSON_THROW_ON_ERROR);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function decodePromotionCursor(string $cursor): array
    {
        $decoded = base64_decode(strtr($cursor, '-_', '+/'), true);
        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid promotion cursor.');
        }

        try {
            /** @var array{c?: mixed, i?: mixed} $payload */
            $payload = json_decode($decoded, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \InvalidArgumentException('Invalid promotion cursor.');
        }

        $code = $payload['c'] ?? null;
        $id = $payload['i'] ?? null;
        if (! is_string($code) || ! is_int($id) || $id < 1) {
            throw new \InvalidArgumentException('Invalid promotion cursor.');
        }

        return [$code, $id];
    }

    public function findById(int $id): ?Promotion
    {
        return Promotion::query()->with('customers')->find($id);
    }

    public function findByCode(string $code): ?Promotion
    {
        return Promotion::query()
            ->with('customers')
            ->whereRaw('UPPER(code) = ?', [strtoupper($code)])
            ->first();
    }

    public function codeExists(string $code, ?int $exceptPromotionId = null): bool
    {
        $query = Promotion::query()->whereRaw('UPPER(code) = ?', [strtoupper($code)]);

        if ($exceptPromotionId !== null) {
            $query->whereKeyNot($exceptPromotionId);
        }

        return $query->exists();
    }

    public function create(array $data, array $customerIds = []): Promotion
    {
        $promotion = Promotion::query()->create([
            'code' => strtoupper($data['code']),
            'name' => $data['name'],
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'stacking_mode' => $data['stacking_mode'],
            'applies_to_all_customers' => $data['applies_to_all_customers'],
            'is_active' => $data['is_active'],
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        if ($customerIds !== []) {
            $promotion->customers()->sync($customerIds);
        }

        return $promotion->fresh(['customers']) ?? $promotion;
    }

    public function update(Promotion $promotion, array $data, ?array $customerIds = null): Promotion
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper((string) $data['code']);
        }

        $promotion->update($data);

        if ($customerIds !== null) {
            $promotion->customers()->sync($customerIds);
        }

        return $promotion->fresh(['customers']) ?? $promotion;
    }

    public function isAssignedToCustomer(int $promotionId, int $customerId): bool
    {
        return Promotion::query()
            ->whereKey($promotionId)
            ->whereHas('customers', fn ($q) => $q->where('customers.id', $customerId))
            ->exists();
    }

    public function listAppliedForSale(int $saleId): Collection
    {
        return SalePromotion::query()
            ->with('promotion')
            ->where('sale_id', $saleId)
            ->get();
    }

    public function findApplied(int $saleId, int $promotionId): ?SalePromotion
    {
        return SalePromotion::query()
            ->where('sale_id', $saleId)
            ->where('promotion_id', $promotionId)
            ->first();
    }

    public function attachToSale(int $saleId, int $promotionId, int $discountAmountCents): SalePromotion
    {
        return SalePromotion::query()->create([
            'sale_id' => $saleId,
            'promotion_id' => $promotionId,
            'discount_amount' => $discountAmountCents,
        ]);
    }

    public function detachFromSale(int $saleId, int $promotionId): void
    {
        SalePromotion::query()
            ->where('sale_id', $saleId)
            ->where('promotion_id', $promotionId)
            ->delete();
    }

    public function updateAppliedAmount(SalePromotion $applied, int $discountAmountCents): void
    {
        $applied->update(['discount_amount' => $discountAmountCents]);
    }
}
