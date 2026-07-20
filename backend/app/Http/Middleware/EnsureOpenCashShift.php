<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\CashShift\Exceptions\CashShiftDomainException;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureOpenCashShift
{
    public function __construct(
        private readonly CashShiftRepositoryInterface $shifts,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $storeId = $request->attributes->get('store_id');

        if ($user === null || ! is_int($storeId)) {
            throw new CashShiftDomainException(ErrorCode::ShiftNotOpen);
        }

        $shift = $this->shifts->findOpenForUserAtStore($user->id, $storeId);

        if ($shift === null) {
            $openElsewhere = $this->shifts->findOpenForUser($user->id);

            if ($openElsewhere !== null && $openElsewhere->store_id !== $storeId) {
                throw new CashShiftDomainException(ErrorCode::ShiftStoreMismatch);
            }

            throw new CashShiftDomainException(ErrorCode::ShiftNotOpen);
        }

        $request->attributes->set('cash_shift_id', $shift->id);

        return $next($request);
    }
}
