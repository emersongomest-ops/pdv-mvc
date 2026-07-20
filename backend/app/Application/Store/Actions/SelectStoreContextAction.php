<?php

declare(strict_types=1);

namespace App\Application\Store\Actions;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Shared\ErrorCode;
use App\Domain\Store\Exceptions\StoreDomainException;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Models\Store;
use App\Models\User;
use App\Support\Store\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class SelectStoreContextAction
{
    public function __construct(
        private readonly StoreRepositoryInterface $stores,
        private readonly StoreContext $storeContext,
    ) {}

    public function execute(User $user, int $storeId, Request $request): Store
    {
        $store = $this->stores->findById($storeId);

        if ($store === null) {
            throw new StoreDomainException(ErrorCode::StoreNotFound);
        }

        if (! Gate::forUser($user)->allows('access', $store)) {
            if (! $store->is_active) {
                throw new StoreDomainException(ErrorCode::StoreInactive);
            }

            throw new AuthenticationDomainException(ErrorCode::AuthStoreAccessDenied);
        }

        $this->storeContext->set($request, $store->id);

        return $store;
    }
}
