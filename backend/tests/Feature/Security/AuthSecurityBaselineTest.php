<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

/**
 * Baseline security gates: login throttle, CSRF allowlist, SPA forgery posture.
 */
final class AuthSecurityBaselineTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();
    }

    #[Test]
    public function login_is_throttled_after_repeated_failures(): void
    {
        User::factory()->operator()->create([
            'email' => 'throttle@pos.test',
            'password' => 'correct-password',
        ]);

        $key = strtolower('throttle@pos.test').'|127.0.0.1';
        RateLimiter::clear($key);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'throttle@pos.test',
                'password' => 'wrong-password',
            ])->assertUnauthorized()
                ->assertJsonPath('error.code', 'AUTH_INVALID_CREDENTIALS');
        }

        // Route middleware `throttle:login` and/or LoginUserAction both cap attempts.
        $this->postJson('/api/auth/login', [
            'email' => 'throttle@pos.test',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    #[Test]
    public function payment_webhook_path_is_csrf_exempt(): void
    {
        $middleware = $this->app->make(PreventRequestForgery::class);
        $request = Request::create('/api/webhooks/payments/stub', 'POST');

        $method = new ReflectionMethod(PreventRequestForgery::class, 'inExceptArray');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($middleware, $request));
    }

    #[Test]
    public function csrf_middleware_rejects_stateful_post_without_token_when_enforced(): void
    {
        $middleware = new class($this->app, $this->app->make('encrypter')) extends PreventRequestForgery
        {
            protected function runningUnitTests(): bool
            {
                return false;
            }
        };

        $request = Request::create('/api/auth/logout', 'POST');
        $request->headers->set('Origin', 'http://localhost');
        $this->startSession();
        $request->setLaravelSession($this->app['session']->driver());

        try {
            $middleware->handle($request, static fn () => response('ok'));
            $this->fail('Expected TokenMismatchException when CSRF is enforced without token.');
        } catch (TokenMismatchException) {
            $this->assertTrue(true);
        }
    }
}
