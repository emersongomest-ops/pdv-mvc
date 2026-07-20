<?php

declare(strict_types=1);

namespace App\Support\Http;

use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Shared\ErrorCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApiErrorResponse
{
    public static function fromErrorCode(ErrorCode $code, array $context = []): JsonResponse
    {
        $error = $code->toErrorPayload();

        if ($context !== []) {
            $error['context'] = $context;
        }

        return response()->json(['error' => $error], $code->httpStatus());
    }

    public static function fromAuthenticationException(AuthenticationException $exception): JsonResponse
    {
        return self::fromErrorCode(ErrorCode::AuthUnauthenticated);
    }

    public static function fromDomainException(AuthenticationDomainException $exception): JsonResponse
    {
        return self::fromErrorCode($exception->errorCode, $exception->context);
    }

    public static function fromAuthorizationException(AuthorizationException $exception): JsonResponse
    {
        return self::fromErrorCode(ErrorCode::AuthForbidden);
    }

    public static function shouldRenderJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
