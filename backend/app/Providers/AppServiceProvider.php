<?php

namespace App\Providers;

use App\Application\Sales\Listeners\NotifyManagersOfSaleCompleted;
use App\Domain\Analytics\Repositories\AnalyticsRepositoryInterface;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\CashShift\Repositories\CashShiftRepositoryInterface;
use App\Domain\Catalog\Repositories\CatalogRepositoryInterface;
use App\Domain\Customers\Repositories\CustomersRepositoryInterface;
use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Domain\IdentityAccess\Services\TotpAuthenticatorInterface;
use App\Domain\Inventory\Repositories\InventoryRepositoryInterface;
use App\Domain\Payments\Card\CardInstrumentValidatorInterface;
use App\Domain\Payments\Gateways\PaymentGatewayInterface;
use App\Domain\Payments\Outbox\PendingPaymentOutboxInterface;
use App\Domain\Payments\Repositories\PaymentsRepositoryInterface;
use App\Domain\Payments\Webhooks\PaymentWebhookPayloadNormalizerInterface;
use App\Domain\Payments\Webhooks\PaymentWebhookSignatureVerifierInterface;
use App\Domain\Payments\Webhooks\WebhookRetryQueueInterface;
use App\Domain\Promotions\Repositories\PromotionsRepositoryInterface;
use App\Domain\RefundsReturns\Repositories\RefundsReturnsRepositoryInterface;
use App\Domain\Sales\Analytics\SaleAnalyticsRecorderInterface;
use App\Domain\Sales\Events\SaleCompleted;
use App\Domain\Sales\Fiscal\FiscalReceiptGeneratorInterface;
use App\Domain\Sales\Repositories\SalesRepositoryInterface;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use App\Infrastructure\Analytics\Persistence\Repositories\AnalyticsRepository;
use App\Infrastructure\Audit\Persistence\Repositories\AuditLogRepository;
use App\Infrastructure\CashShift\Persistence\Repositories\CashShiftRepository;
use App\Infrastructure\Catalog\Persistence\Repositories\CatalogRepository;
use App\Infrastructure\Customers\Persistence\Repositories\CustomersRepository;
use App\Infrastructure\IdentityAccess\Persistence\Repositories\UsersRepository;
use App\Infrastructure\IdentityAccess\Totp\Google2faTotpAuthenticator;
use App\Infrastructure\Inventory\Persistence\Repositories\InventoryRepository;
use App\Infrastructure\Payments\Card\NotImplementedCardInstrumentValidator;
use App\Infrastructure\Payments\Gateways\SoapPaymentGateway;
use App\Infrastructure\Payments\Outbox\InMemoryPendingPaymentOutbox;
use App\Infrastructure\Payments\Outbox\RedisPendingPaymentOutbox;
use App\Infrastructure\Payments\Persistence\Repositories\PaymentsRepository;
use App\Infrastructure\Payments\Webhooks\HmacPaymentWebhookSignatureVerifier;
use App\Infrastructure\Payments\Webhooks\InMemoryWebhookRetryQueue;
use App\Infrastructure\Payments\Webhooks\JsonPaymentWebhookPayloadNormalizer;
use App\Infrastructure\Payments\Webhooks\RedisWebhookRetryQueue;
use App\Infrastructure\Promotions\Persistence\Repositories\PromotionsRepository;
use App\Infrastructure\RefundsReturns\Persistence\Repositories\RefundsReturnsRepository;
use App\Infrastructure\Sales\Analytics\LogSaleAnalyticsRecorder;
use App\Infrastructure\Sales\Fiscal\StubFiscalReceiptGenerator;
use App\Infrastructure\Sales\Persistence\Repositories\SalesRepository;
use App\Domain\Shared\Idempotency\IdempotencyRecordRepositoryInterface;
use App\Infrastructure\Shared\Idempotency\EloquentIdempotencyRecordRepository;
use App\Infrastructure\Store\Persistence\Repositories\StoreRepository;
use App\Models\User;
use App\Support\Security\ProductionSecurityConfigAssertor;
use App\Support\Store\StoreContext;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Telescope\TelescopeServiceProvider as TelescopePackageServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(AnalyticsRepositoryInterface::class, AnalyticsRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
        $this->app->bind(CashShiftRepositoryInterface::class, CashShiftRepository::class);
        $this->app->bind(CatalogRepositoryInterface::class, CatalogRepository::class);
        $this->app->bind(CustomersRepositoryInterface::class, CustomersRepository::class);
        $this->app->bind(UsersRepositoryInterface::class, UsersRepository::class);
        $this->app->bind(TotpAuthenticatorInterface::class, Google2faTotpAuthenticator::class);
        $this->app->bind(PromotionsRepositoryInterface::class, PromotionsRepository::class);
        $this->app->bind(RefundsReturnsRepositoryInterface::class, RefundsReturnsRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(SalesRepositoryInterface::class, SalesRepository::class);
        $this->app->bind(PaymentsRepositoryInterface::class, PaymentsRepository::class);
        $this->app->bind(PaymentGatewayInterface::class, SoapPaymentGateway::class);
        $this->app->bind(CardInstrumentValidatorInterface::class, NotImplementedCardInstrumentValidator::class);
        $this->app->bind(PaymentWebhookSignatureVerifierInterface::class, HmacPaymentWebhookSignatureVerifier::class);
        $this->app->bind(PaymentWebhookPayloadNormalizerInterface::class, JsonPaymentWebhookPayloadNormalizer::class);
        $this->app->singleton(PendingPaymentOutboxInterface::class, function () {
            return (string) config('payments.reconcile.driver', 'redis') === 'array'
                ? new InMemoryPendingPaymentOutbox
                : new RedisPendingPaymentOutbox;
        });
        $this->app->singleton(WebhookRetryQueueInterface::class, function () {
            return (string) config('payments.reconcile.driver', 'redis') === 'array'
                ? new InMemoryWebhookRetryQueue
                : new RedisWebhookRetryQueue;
        });
        $this->app->bind(FiscalReceiptGeneratorInterface::class, StubFiscalReceiptGenerator::class);
        $this->app->bind(SaleAnalyticsRecorderInterface::class, LogSaleAnalyticsRecorder::class);
        $this->app->bind(IdempotencyRecordRepositoryInterface::class, EloquentIdempotencyRecordRepository::class);
        $this->app->singleton(StoreContext::class);

        if (
            $this->app->environment('local')
            && config('telescope.enabled')
            && class_exists(TelescopePackageServiceProvider::class)
        ) {
            $this->app->register(TelescopePackageServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        (new ProductionSecurityConfigAssertor)->assertForEnvironment((string) $this->app->environment());

        Password::defaults(static function (): Password {
            $rule = Password::min(12);

            if ((bool) config('auth.password_uncompromised', true)) {
                $rule = $rule->uncompromised();
            }

            return $rule;
        });

        Event::listen(SaleCompleted::class, NotifyManagersOfSaleCompleted::class);

        Gate::define('viewPulse', function (User $user): bool {
            return $user->is_active && $user->isManager();
        });

        RateLimiter::for('login', function (Request $request): Limit {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by(strtolower($email).'|'.$request->ip());
        });

        RateLimiter::for('refunds', function (Request $request): Limit {
            $userId = $request->user()?->getAuthIdentifier() ?? 'guest';

            return Limit::perMinute(10)->by($userId.'|'.$request->ip());
        });

        RateLimiter::for('mfa', function (Request $request): Limit {
            $pending = $request->session()->get('mfa.pending_user_id', 'guest');

            return Limit::perMinute(10)->by($pending.'|'.$request->ip());
        });
    }
}
