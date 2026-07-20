<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Application\IdentityAccess\Support\MfaPendingSession;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Services\TotpAuthenticatorInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\User;
use Illuminate\Contracts\Session\Session;

final class BeginManagerMfaSetupAction
{
    public function __construct(
        private readonly MfaPendingSession $mfaPending,
        private readonly TotpAuthenticatorInterface $totp,
    ) {}

    /**
     * @return array{secret: string, otpauth_url: string, qr_data_uri: string}
     */
    public function execute(Session $session): array
    {
        $user = $this->pendingManager($session);

        if ($user->hasMfaEnabled()) {
            throw new AuthenticationDomainException(ErrorCode::AuthMfaAlreadyEnabled);
        }

        $secret = $this->totp->generateSecret();
        $user->forceFill([
            'mfa_secret' => $secret,
            'mfa_confirmed_at' => null,
            'mfa_last_otp_timestamp' => null,
        ])->save();

        $otpAuthUrl = $this->totp->otpAuthUrl('PDV', $user->email, $secret);

        return [
            'secret' => $secret,
            'otpauth_url' => $otpAuthUrl,
            'qr_data_uri' => $this->totp->qrSvgDataUri($otpAuthUrl),
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
