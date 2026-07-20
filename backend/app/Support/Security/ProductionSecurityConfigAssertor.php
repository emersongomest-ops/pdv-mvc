<?php

declare(strict_types=1);

namespace App\Support\Security;

use RuntimeException;

/**
 * Fail-closed production posture (ASVS V7/V3 — APP_DEBUG + Secure cookies).
 */
final class ProductionSecurityConfigAssertor
{
    public function assertForEnvironment(string $environment): void
    {
        if ($environment !== 'production') {
            return;
        }

        if ((bool) config('app.debug') === true) {
            throw new RuntimeException(
                'APP_DEBUG must be false when APP_ENV=production (ASVS / production-hardening).',
            );
        }

        if ((bool) config('session.secure') !== true) {
            throw new RuntimeException(
                'SESSION_SECURE_COOKIE must be true when APP_ENV=production (TLS required).',
            );
        }
    }
}
