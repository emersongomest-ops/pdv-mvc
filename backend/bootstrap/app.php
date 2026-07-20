<?php

use App\Domain\CashShift\Exceptions\CashShiftDomainException;
use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\Inventory\Exceptions\InventoryDomainException;
use App\Domain\Catalog\Exceptions\CatalogDomainException;
use App\Domain\Payments\Exceptions\PaymentDomainException;
use App\Domain\Promotions\Exceptions\PromotionDomainException;
use App\Domain\RefundsReturns\Exceptions\RefundDomainException;
use App\Domain\Sales\Exceptions\SaleDomainException;
use App\Domain\Shared\Exceptions\IdempotencyDomainException;
use App\Domain\Store\Exceptions\StoreDomainException;
use App\Http\Middleware\AssignCorrelationId;
use App\Http\Middleware\EnsureOpenCashShift;
use App\Http\Middleware\EnsureStoreContext;
use App\Http\Middleware\EnsureUserHasRole;
use App\Support\Http\ApiErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function (): void {
            // ADR-0011: versioned alias of the same route table (no SPA cutover yet).
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->append(AssignCorrelationId::class);
        $middleware->validateCsrfTokens(except: [
            'api/webhooks/payments/*',
            'api/v1/webhooks/payments/*',
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'store.context' => EnsureStoreContext::class,
            'shift.open' => EnsureOpenCashShift::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (AuthenticationDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromDomainException($exception);
        });

        $exceptions->render(function (StoreDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (CashShiftDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (SaleDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (PaymentDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (CatalogDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (InventoryDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (CustomerDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (PromotionDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (RefundDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (IdempotencyDomainException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromErrorCode($exception->errorCode);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromAuthenticationException($exception);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if (! ApiErrorResponse::shouldRenderJson($request)) {
                return null;
            }

            return ApiErrorResponse::fromAuthorizationException($exception);
        });
    })->create();
