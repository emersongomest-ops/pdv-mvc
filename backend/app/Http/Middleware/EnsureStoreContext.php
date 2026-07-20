<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Shared\ErrorCode;
use App\Domain\Store\Exceptions\StoreDomainException;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Support\Store\StoreContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class EnsureStoreContext
{
    public function __construct(
        private readonly StoreContext $storeContext,
        private readonly StoreRepositoryInterface $stores,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $storeId = $this->storeContext->current($request);

        if ($storeId === null) {
            throw new AuthenticationDomainException(ErrorCode::AuthStoreContextRequired);
        }

        $store = $this->stores->findById($storeId);

        if ($store === null) {
            throw new StoreDomainException(ErrorCode::StoreNotFound);
        }

        if (! $store->is_active) {
            throw new StoreDomainException(ErrorCode::StoreInactive);
        }

        $user = $request->user();

        if ($user === null || ! Gate::forUser($user)->allows('access', $store)) {
            throw new AuthenticationDomainException(ErrorCode::AuthStoreAccessDenied);
        }

        $request->attributes->set('store_id', $storeId);

        return $next($request);
    }
}
