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

final class CreatePromotionAction
{
    public function __construct(
        private readonly PromotionsRepositoryInterface $promotions,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $customerIds
     */
    public function execute(User $actor, array $data, array $customerIds = []): Promotion
    {
        $code = strtoupper(trim((string) $data['code']));

        if ($this->promotions->codeExists($code)) {
            throw new PromotionDomainException(ErrorCode::PromoNotApplicable);
        }

        return DB::transaction(function () use ($actor, $data, $code, $customerIds): Promotion {
            $promotion = $this->promotions->create([
                'code' => $code,
                'name' => (string) $data['name'],
                'discount_type' => (string) $data['discount_type'],
                'discount_value' => (int) $data['discount_value'],
                'stacking_mode' => (string) $data['stacking_mode'],
                'applies_to_all_customers' => (bool) ($data['applies_to_all_customers'] ?? false),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
            ], $customerIds);

            $this->auditLogs->append(new AuditLogEntry(
                action: AuditAction::PromotionCreated,
                actorUserId: (int) $actor->id,
                subjectType: 'promotion',
                subjectId: (int) $promotion->id,
                storeId: null,
                oldValues: null,
                newValues: PromotionAuditSnapshot::from($promotion),
            ));

            return $promotion;
        });
    }
}
