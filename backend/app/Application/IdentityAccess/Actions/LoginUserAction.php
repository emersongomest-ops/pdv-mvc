<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Application\IdentityAccess\Support\MfaPendingSession;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Services\TurnstileVerifierInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class LoginUserAction
{
    public function __construct(
        private readonly MfaPendingSession $mfaPending,
        private readonly TurnstileVerifierInterface $turnstile,
    ) {}

    /**
     * @return array{
     *     user: User,
     *     mfa_required: bool,
     *     mfa_setup_required: bool
     * }
     */
    public function execute(
        string $email,
        string $password,
        Session $session,
        ?string $turnstileToken = null,
    ): array {
        $throttleKey = Str::lower($email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw new AuthenticationDomainException(ErrorCode::AuthTooManyAttempts);
        }

        $this->assertCaptchaIfRequired($throttleKey, $turnstileToken);

        if (! Auth::attempt(['email' => $email, 'password' => $password, 'is_active' => true])) {
            RateLimiter::hit($throttleKey, 60);

            $user = User::query()->where('email', $email)->first();

            if ($user !== null && ! $user->is_active) {
                throw new AuthenticationDomainException(
                    ErrorCode::AuthAccountInactive,
                    $this->captchaContext($throttleKey),
                );
            }

            throw new AuthenticationDomainException(
                ErrorCode::AuthInvalidCredentials,
                $this->captchaContext($throttleKey),
            );
        }

        RateLimiter::clear($throttleKey);

        /** @var User $user */
        $user = Auth::user();

        if ($user->isManager()) {
            Auth::logout();
            $session->regenerate();
            $this->mfaPending->put($session, $user->id);

            return [
                'user' => $user,
                'mfa_required' => true,
                'mfa_setup_required' => ! $user->hasMfaEnabled(),
            ];
        }

        $session->regenerate();
        $this->mfaPending->forget($session);

        return [
            'user' => $user,
            'mfa_required' => false,
            'mfa_setup_required' => false,
        ];
    }

    private function assertCaptchaIfRequired(string $throttleKey, ?string $turnstileToken): void
    {
        if (! $this->turnstile->isEnabled()) {
            return;
        }

        if (RateLimiter::attempts($throttleKey) < $this->turnstile->failureThreshold()) {
            return;
        }

        if (! is_string($turnstileToken) || $turnstileToken === '') {
            throw new AuthenticationDomainException(
                ErrorCode::AuthCaptchaRequired,
                $this->captchaContext($throttleKey, forceRequired: true),
            );
        }

        if (! $this->turnstile->verify($turnstileToken, request()->ip())) {
            throw new AuthenticationDomainException(
                ErrorCode::AuthCaptchaInvalid,
                $this->captchaContext($throttleKey, forceRequired: true),
            );
        }
    }

    /**
     * @return array{captcha_required: bool, turnstile_site_key?: string}
     */
    private function captchaContext(string $throttleKey, bool $forceRequired = false): array
    {
        if (! $this->turnstile->isEnabled()) {
            return ['captcha_required' => false];
        }

        $required = $forceRequired
            || RateLimiter::attempts($throttleKey) >= $this->turnstile->failureThreshold();

        $context = ['captcha_required' => $required];

        if ($required) {
            $siteKey = (string) config('services.turnstile.site_key', '');
            if ($siteKey !== '') {
                $context['turnstile_site_key'] = $siteKey;
            }
        }

        return $context;
    }
}
