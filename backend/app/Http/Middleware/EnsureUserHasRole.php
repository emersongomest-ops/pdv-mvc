<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\IdentityAccess\ValueObjects\UserRole;
use App\Domain\Shared\ErrorCode;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            throw new AuthenticationDomainException(ErrorCode::AuthUnauthenticated);
        }

        if (! $user->is_active) {
            throw new AuthenticationDomainException(ErrorCode::AuthAccountInactive);
        }

        $allowed = array_map(
            static fn (string $role): UserRole => UserRole::from($role),
            $roles
        );

        if (! in_array($user->role, $allowed, true)) {
            $onlyManager = $allowed === [UserRole::Manager];

            throw new AuthenticationDomainException(
                $onlyManager ? ErrorCode::AuthAdminOnly : ErrorCode::AuthRoleDenied
            );
        }

        return $next($request);
    }
}
