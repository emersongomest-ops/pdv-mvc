<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Application\IdentityAccess\Support\MfaPendingSession;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Services\MfaRecoveryCodeVault;
use App\Domain\IdentityAccess\Services\TotpAuthenticatorInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

final class VerifyManagerMfaChallengeAction
{
    public function __construct(
        private readonly MfaPendingSession $mfaPending,
        private readonly TotpAuthenticatorInterface $totp,
        private readonly MfaRecoveryCodeVault $recoveryCodes,
    ) {}

    /**
     * @return array{user: User}
     */
    public function execute(Session $session, string $code): array
    {
        $user = $this->pendingManager($session);
        $throttleKey = 'mfa-verify|'.$user->id.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw new AuthenticationDomainException(ErrorCode::AuthTooManyAttempts);
        }

        if (! $user->hasMfaEnabled() || $user->mfa_secret === null || $user->mfa_secret === '') {
            throw new AuthenticationDomainException(ErrorCode::AuthMfaSetupRequired);
        }

        $timestamp = $this->totp->verifyAndTimestamp(
            $user->mfa_secret,
            $code,
            $user->mfa_last_otp_timestamp,
        );

        if ($timestamp !== false) {
            RateLimiter::clear($throttleKey);
            $user->forceFill([
                'mfa_last_otp_timestamp' => $timestamp,
            ])->save();

            return $this->completeLogin($session, $user);
        }

        /** @var list<string>|null $stored */
        $stored = $user->mfa_recovery_codes;
        $consumed = $this->recoveryCodes->consume(is_array($stored) ? $stored : null, $code);

        if (! $consumed['matched']) {
            RateLimiter::hit($throttleKey, 60);
            throw new AuthenticationDomainException(ErrorCode::AuthMfaInvalidCode);
        }

        RateLimiter::clear($throttleKey);
        $user->forceFill([
            'mfa_recovery_codes' => $consumed['hashes'],
        ])->save();

        return $this->completeLogin($session, $user);
    }

    /**
     * @return array{user: User}
     */
    private function completeLogin(Session $session, User $user): array
    {
        $this->mfaPending->forget($session);
        Auth::login($user);
        $session->regenerate();

        return ['user' => $user];
    }

    private function pendingManager(Session $session): User
    {
        $userId = $this->mfaPending->userId($session);

        if ($userId === null) {
            throw new AuthenticationDomainException(ErrorCode::AuthMfaNotPending);
        }

        $user = User::query()->find($userId);

        if ($user === null || ! $user->is_active || ! $user->isManager()) {
            $this->mfaPending->forget($session);
            throw new AuthenticationDomainException(ErrorCode::AuthMfaNotPending);
        }

        return $user;
    }
}
