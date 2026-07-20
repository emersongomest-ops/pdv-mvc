<?php

declare(strict_types=1);

namespace App\Application\CashShift\Actions;

use App\Domain\CashShift\DTOs\ShiftClosingReport;
use App\Domain\CashShift\Exceptions\CashShiftDomainException;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\CashShift;
use App\Models\User;

final class CloseCashShiftAction
{
    public function __construct(
        private readonly CashShiftRepositoryInterface $shifts,
    ) {}

    /**
     * @return array{shift: CashShift, report: ShiftClosingReport}
     */
    public function execute(User $user, int $storeId, ?int $closingCashAmountCents): array
    {
        $shift = $this->shifts->findOpenForUserAtStore($user->id, $storeId);

        if ($shift === null) {
            throw new CashShiftDomainException(ErrorCode::ShiftNotOpen);
        }

        $closed = $this->shifts->close($shift, $closingCashAmountCents);

        return [
            'shift' => $closed,
            'report' => $this->shifts->buildClosingReport($closed),
        ];
    }
}
