<?php

declare(strict_types=1);

namespace App\Application\Promotions\Actions;

use App\Application\Promotions\Support\PromotionAuditSnapshot;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\Promotions\Exceptions\PromotionDomainException;
use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdatePromotionAction
{
    public function __construct(
        private readonly PromotionsRepositoryInterface $promotions,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>|null  $customerIds
     */
    public function execute(User $actor, int $promotionId, array $data, ?array $customerIds = null): Promotion
    {
        $promotion = $this->promotions->findById($promotionId);

        if ($promotion === null) {
            throw new PromotionDomainException(ErrorCode::PromoNotFound);
        }

        $before = PromotionAuditSnapshot::from($promotion);
        $updates = [];

        foreach (['name', 'discount_type', 'stacking_mode', 'starts_at', 'ends_at'] as $field) {
            if (array_key_exists($field, $data)) {
                $updates[$field] = $data[$field];
            }
        }

        if (array_key_exists('code', $data)) {
            $code = strtoupper(trim((string) $data['code']));
            if ($this->promotions->codeExists($code, $promotionId)) {
                throw new PromotionDomainException(ErrorCode::PromoNotApplicable);
            }
            $updates['code'] = $code;
        }

        if (array_key_exists('discount_value', $data)) {
            $updates['discount_value'] = (int) $data['discount_value'];
        }

        if (array_key_exists('applies_to_all_customers', $data)) {
            $updates['applies_to_all_customers'] = (bool) $data['applies_to_all_customers'];
        }

        if (array_key_exists('is_active', $data)) {
            $updates['is_active'] = (bool) $data['is_active'];
        }

        return DB::transaction(function () use ($actor, $promotion, $updates, $customerIds, $before): Promotion {
            $updated = $this->promotions->update($promotion, $updates, $customerIds);
            $after = PromotionAuditSnapshot::from($updated);

            if ($before !== $after) {
                $this->auditLogs->append(new AuditLogEntry(
                    action: AuditAction::PromotionUpdated,
                    actorUserId: (int) $actor->id,
                    subjectType: 'promotion',
                    subjectId: (int) $updated->id,
                    storeId: null,
                    oldValues: $before,
                    newValues: $after,
                ));
            }

            return $updated;
        });
    }
}
