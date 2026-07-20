<?php

declare(strict_types=1);

namespace App\Application\CashShift\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\CashShift\Exceptions\CashShiftDomainException;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\CashShift\ValueObjects\CashShiftStatus;
use App\Domain\Shared\ErrorCode;
use App\Models\CashShift;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ReopenCashShiftAction
{
    public function __construct(
        private readonly CashShiftRepositoryInterface $shifts,
        private readonly AssertManagerStoreAccess $storeAccess,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    public function execute(User $manager, int $shiftId): CashShift
    {
        $shift = $this->shifts->findById($shiftId);

        if ($shift === null) {
            throw new CashShiftDomainException(ErrorCode::ShiftNotFound);
        }

        $this->storeAccess->assertCanAccess($manager, (int) $shift->store_id);

        if ($shift->status !== CashShiftStatus::Closed) {
            throw new CashShiftDomainException(ErrorCode::ShiftAlreadyOpen);
        }

        $openForOperator = $this->shifts->findOpenForUser((int) $shift->user_id);
        if ($openForOperator !== null) {
            throw new CashShiftDomainException(ErrorCode::ShiftAlreadyOpen);
        }

        $oldStatus = $shift->status->value;
        $oldClosing = $shift->closing_cash_amount;
        $oldClosedAt = $shift->closed_at?->toIso8601String();

        return DB::transaction(function () use ($manager, $shift, $oldStatus, $oldClosing, $oldClosedAt): CashShift {
            $reopened = $this->shifts->reopen($shift);

            $this->auditLogs->append(new AuditLogEntry(
                action: AuditAction::CashShiftReopened,
                actorUserId: (int) $manager->id,
                subjectType: 'cash_shift',
                subjectId: (int) $reopened->id,
                storeId: (int) $reopened->store_id,
                oldValues: [
                    'status' => $oldStatus,
                    'closing_cash_amount' => $oldClosing,
                    'closed_at' => $oldClosedAt,
                ],
                newValues: [
                    'status' => $reopened->status->value,
                    'closing_cash_amount' => null,
                    'closed_at' => null,
                ],
                metadata: [
                    'operator_id' => (int) $reopened->user_id,
                ],
            ));

            return $reopened;
        });
    }
}
