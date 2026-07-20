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

final class ConfirmManagerMfaSetupAction
{
    public function __construct(
        private readonly MfaPendingSession $mfaPending,
        private readonly TotpAuthenticatorInterface $totp,
        private readonly MfaRecoveryCodeVault $recoveryCodes,
    ) {}

    /**
     * @return array{user: User, recovery_codes: list<string>}
     */
    public function execute(Session $session, string $code): array
    {
        $user = $this->pendingManager($session);
        $throttleKey = 'mfa-confirm|'.$user->id.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw new AuthenticationDomainException(ErrorCode::AuthTooManyAttempts);
        }

        if ($user->hasMfaEnabled()) {
            throw new AuthenticationDomainException(ErrorCode::AuthMfaAlreadyEnabled);
        }

        if ($user->mfa_secret === null || $user->mfa_secret === '') {
            throw new AuthenticationDomainException(ErrorCode::AuthMfaSetupRequired);
        }

        $timestamp = $this->totp->verifyAndTimestamp(
            $user->mfa_secret,
            $code,
            $user->mfa_last_otp_timestamp,
        );

        if ($timestamp === false) {
            RateLimiter::hit($throttleKey, 60);
            throw new AuthenticationDomainException(ErrorCode::AuthMfaInvalidCode);
        }

        RateLimiter::clear($throttleKey);

        $plainRecovery = $this->recoveryCodes->generatePlaintexts();

        $user->forceFill([
            'mfa_confirmed_at' => now(),
            'mfa_last_otp_timestamp' => $timestamp,
            'mfa_recovery_codes' => $this->recoveryCodes->hashAll($plainRecovery),
        ])->save();

        $this->mfaPending->forget($session);
        Auth::login($user);
        $session->regenerate();

        return [
            'user' => $user,
            'recovery_codes' => $plainRecovery,
        ];
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
