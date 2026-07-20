<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Services;

/**
 * One-time MFA recovery codes (plaintexts returned once; only hashes persisted).
 */
final class MfaRecoveryCodeVault
{
    public const COUNT = 8;

    public const CODE_LENGTH = 10;

    /**
     * @return list<string> plaintext codes (show once)
     */
    public function generatePlaintexts(): array
    {
        $codes = [];

        for ($i = 0; $i < self::COUNT; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(self::CODE_LENGTH / 2)));
        }

        return $codes;
    }

    /**
     * @param  list<string>  $plaintexts
     * @return list<string> password hashes
     */
    public function hashAll(array $plaintexts): array
    {
        return array_values(array_map(
            static fn (string $code): string => password_hash($code, PASSWORD_BCRYPT),
            $plaintexts,
        ));
    }

    /**
     * @param  list<string>|null  $hashes
     * @return array{matched: bool, hashes: list<string>}
     */
    public function consume(?array $hashes, string $candidate): array
    {
        $normalized = strtoupper(preg_replace('/\s+/', '', $candidate) ?? '');
        $remaining = [];
        $matched = false;

        foreach ($hashes ?? [] as $hash) {
            if (! is_string($hash) || $hash === '') {
                continue;
            }

            if (! $matched && password_verify($normalized, $hash)) {
                $matched = true;

                continue;
            }

            $remaining[] = $hash;
        }

        return [
            'matched' => $matched,
            'hashes' => $remaining,
        ];
    }
}
