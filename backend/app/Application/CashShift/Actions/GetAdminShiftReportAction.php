<?php

declare(strict_types=1);

namespace App\Application\CashShift\Actions;

use App\Application\Store\Support\AssertManagerStoreAccess;
use App\Domain\CashShift\DTOs\ShiftClosingReport;
use App\Domain\CashShift\Exceptions\CashShiftDomainException;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\User;

final class GetAdminShiftReportAction
{
    public function __construct(
        private readonly CashShiftRepositoryInterface $shifts,
        private readonly AssertManagerStoreAccess $storeAccess,
    ) {}

    public function execute(User $manager, int $shiftId): ShiftClosingReport
    {
        $shift = $this->shifts->findById($shiftId);

        if ($shift === null) {
            throw new CashShiftDomainException(ErrorCode::ShiftNotFound);
        }

        $this->storeAccess->assertCanAccess($manager, (int) $shift->store_id);

        return $this->shifts->buildClosingReport($shift);
    }
}
