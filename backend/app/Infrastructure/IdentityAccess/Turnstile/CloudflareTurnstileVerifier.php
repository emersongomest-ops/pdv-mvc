<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Turnstile;

use App\Domain\IdentityAccess\Services\TurnstileVerifierInterface;
use Illuminate\Support\Facades\Http;
use Throwable;

final class CloudflareTurnstileVerifier implements TurnstileVerifierInterface
{
    private const SITEVERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function isEnabled(): bool
    {
        if (! (bool) config('services.turnstile.enabled', false)) {
            return false;
        }

        $secret = (string) config('services.turnstile.secret_key', '');

        return $secret !== '';
    }

    public function failureThreshold(): int
    {
        return max(1, (int) config('services.turnstile.failure_threshold', 2));
    }

    public function verify(?string $token, ?string $remoteIp): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (! is_string($token) || $token === '') {
            return false;
        }

        try {
            $payload = [
                'secret' => (string) config('services.turnstile.secret_key'),
                'response' => $token,
            ];

            if (is_string($remoteIp) && $remoteIp !== '') {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::asForm()
                ->timeout(5)
                ->post(self::SITEVERIFY_URL, $payload);

            if (! $response->successful()) {
                return false;
            }

            /** @var array{success?: mixed} $body */
            $body = $response->json() ?? [];

            return ($body['success'] ?? false) === true;
        } catch (Throwable) {
            return false;
        }
    }
}
