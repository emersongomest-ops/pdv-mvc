<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Services;

interface TurnstileVerifierInterface
{
    public function isEnabled(): bool;

    /**
     * Failures after which login requires a Turnstile token (inclusive).
     */
    public function failureThreshold(): int;

    public function verify(?string $token, ?string $remoteIp): bool;
}
