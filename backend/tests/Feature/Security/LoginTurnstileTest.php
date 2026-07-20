<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\InteractsWithStatefulApi;
use Tests\TestCase;

final class LoginTurnstileTest extends TestCase
{
    use InteractsWithStatefulApi;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableStatefulApiHeaders();

        config([
            'services.turnstile.enabled' => true,
            'services.turnstile.secret_key' => 'test-secret',
            'services.turnstile.site_key' => '1x00000000000000000000AA',
            'services.turnstile.failure_threshold' => 2,
        ]);
    }

    #[Test]
    public function captcha_required_after_threshold_failures(): void
    {
        User::factory()->operator()->create([
            'email' => 'captcha@pos.test',
            'password' => 'correct-password',
        ]);

        $key = strtolower('captcha@pos.test').'|127.0.0.1';
        RateLimiter::clear($key);

        $this->postJson('/api/auth/login', [
            'email' => 'captcha@pos.test',
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'AUTH_INVALID_CREDENTIALS')
            ->assertJsonPath('error.context.captcha_required', false);

        $this->postJson('/api/auth/login', [
            'email' => 'captcha@pos.test',
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('error.context.captcha_required', true)
            ->assertJsonPath('error.context.turnstile_site_key', '1x00000000000000000000AA');

        $this->postJson('/api/auth/login', [
            'email' => 'captcha@pos.test',
            'password' => 'correct-password',
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_CAPTCHA_REQUIRED')
            ->assertJsonPath('error.context.turnstile_site_key', '1x00000000000000000000AA');
    }

    #[Test]
    public function valid_turnstile_token_allows_login_after_failures(): void
    {
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        User::factory()->operator()->create([
            'email' => 'ok-captcha@pos.test',
            'password' => 'correct-password',
        ]);

        $key = strtolower('ok-captcha@pos.test').'|127.0.0.1';
        RateLimiter::clear($key);
        RateLimiter::hit($key, 60);
        RateLimiter::hit($key, 60);

        $this->postJson('/api/auth/login', [
            'email' => 'ok-captcha@pos.test',
            'password' => 'correct-password',
            'turnstile_token' => 'valid-token',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.email', 'ok-captcha@pos.test');
    }

    #[Test]
    public function invalid_turnstile_token_is_rejected(): void
    {
        Http::fake([
            'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false], 200),
        ]);

        User::factory()->operator()->create([
            'email' => 'bad-captcha@pos.test',
            'password' => 'correct-password',
        ]);

        $key = strtolower('bad-captcha@pos.test').'|127.0.0.1';
        RateLimiter::clear($key);
        RateLimiter::hit($key, 60);
        RateLimiter::hit($key, 60);

        $this->postJson('/api/auth/login', [
            'email' => 'bad-captcha@pos.test',
            'password' => 'correct-password',
            'turnstile_token' => 'bad-token',
        ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'AUTH_CAPTCHA_INVALID');
    }
}
