<?php

declare(strict_types=1);

namespace App\Application\Store\Support;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Shared\ErrorCode;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Models\User;

/**
 * Admin multi-store gate (RN-064): assignment via store_user, not session context.
 */
final class AssertManagerStoreAccess
{
    public function __construct(
        private readonly StoreRepositoryInterface $stores,
    ) {}

    public function assertCanAccess(User $user, int $storeId): void
    {
        if (! $this->stores->userCanAccessStore($user, $storeId)) {
            throw new AuthenticationDomainException(ErrorCode::AuthStoreAccessDenied);
        }
    }

    /**
     * @return list<int>
     */
    public function assignedStoreIds(User $user): array
    {
        return $this->stores->assignedStoreIds($user);
    }
}
