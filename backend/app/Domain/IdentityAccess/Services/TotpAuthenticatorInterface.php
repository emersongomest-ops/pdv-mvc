<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Services;

interface TotpAuthenticatorInterface
{
    public function generateSecret(): string;

    public function otpAuthUrl(string $company, string $email, string $secret): string;

    public function qrSvgDataUri(string $otpAuthUrl): string;

    /**
     * @return int|false Unix timestamp window used when valid; false when invalid/reused
     */
    public function verifyAndTimestamp(string $secret, string $code, ?int $previousTimestamp): int|false;

    public function currentOtp(string $secret): string;
}
