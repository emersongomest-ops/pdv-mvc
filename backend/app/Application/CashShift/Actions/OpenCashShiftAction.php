<?php

declare(strict_types=1);

namespace App\Application\CashShift\Actions;

use App\Domain\CashShift\Exceptions\CashShiftDomainException;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\CashShift;
use App\Models\User;

final class OpenCashShiftAction
{
    public function __construct(
        private readonly CashShiftRepositoryInterface $shifts,
    ) {}

    public function execute(User $user, int $storeId, int $openingCashAmountCents): CashShift
    {
        $existing = $this->shifts->findOpenForUser($user->id);

        if ($existing !== null) {
            throw new CashShiftDomainException(ErrorCode::ShiftAlreadyOpen);
        }

        return $this->shifts->createOpen($storeId, $user->id, $openingCashAmountCents);
    }
}
