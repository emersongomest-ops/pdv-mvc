<?php

declare(strict_types=1);

namespace App\Support\Pii;

use Illuminate\Encryption\Encrypter;
use RuntimeException;

final class PiiCrypto
{
    private static ?Encrypter $encrypter = null;

    public static function encrypter(): Encrypter
    {
        if (self::$encrypter instanceof Encrypter) {
            return self::$encrypter;
        }

        $key = self::parseKey((string) config('pii.encryption_key'));

        self::$encrypter = new Encrypter($key, 'AES-256-CBC');

        return self::$encrypter;
    }

    public static function encryptString(string $value): string
    {
        return self::encrypter()->encryptString($value);
    }

    public static function decryptString(string $payload): string
    {
        return self::encrypter()->decryptString($payload);
    }

    public static function blindIndex(string $normalizedValue): string
    {
        $key = self::parseKey((string) config('pii.blind_index_key'));

        return hash_hmac('sha256', $normalizedValue, $key);
    }

    public static function normalizeCpf(string $cpf): string
    {
        return preg_replace('/\D+/', '', $cpf) ?? '';
    }

    public static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    public static function maskCpf(string $cpf): string
    {
        $digits = self::normalizeCpf($cpf);
        if (strlen($digits) !== 11) {
            return '***.***.***-**';
        }

        return substr($digits, 0, 3).'.***.***-'.substr($digits, -2);
    }

    private static function parseKey(string $key): string
    {
        if ($key === '') {
            throw new RuntimeException('PII encryption key is not configured.');
        }

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded === false || strlen($decoded) !== 32) {
                throw new RuntimeException('PII encryption key must decode to 32 bytes.');
            }

            return $decoded;
        }

        if (strlen($key) === 32) {
            return $key;
        }

        throw new RuntimeException('PII encryption key must be base64:... or a 32-byte string.');
    }
}
