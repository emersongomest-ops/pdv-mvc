<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Totp;

use App\Domain\IdentityAccess\Services\TotpAuthenticatorInterface;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

final class Google2faTotpAuthenticator implements TotpAuthenticatorInterface
{
    private const WINDOW = 1;

    public function __construct(
        private readonly Google2FA $google2fa = new Google2FA,
    ) {}

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function otpAuthUrl(string $company, string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl($company, $email, $secret);
    }

    public function qrSvgDataUri(string $otpAuthUrl): string
    {
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(220),
                new SvgImageBackEnd,
            ),
        );

        $svg = $writer->writeString($otpAuthUrl);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public function verifyAndTimestamp(string $secret, string $code, ?int $previousTimestamp): int|false
    {
        $normalized = preg_replace('/\s+/', '', $code) ?? '';

        if ($normalized === '' || ! ctype_digit($normalized)) {
            return false;
        }

        return $this->google2fa->verifyKeyNewer(
            $secret,
            $normalized,
            $previousTimestamp ?? 0,
            self::WINDOW,
        );
    }

    public function currentOtp(string $secret): string
    {
        return $this->google2fa->getCurrentOtp($secret);
    }
}
