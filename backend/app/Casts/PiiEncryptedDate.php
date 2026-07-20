<?php

declare(strict_types=1);

namespace App\Casts;

use App\Support\Pii\PiiCrypto;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @implements CastsAttributes<\Illuminate\Support\Carbon|null, \DateTimeInterface|string|null>
 */
final class PiiEncryptedDate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CarbonInterface
    {
        if ($value === null || $value === '') {
            return null;
        }

        $plain = PiiCrypto::decryptString((string) $value);

        return Carbon::parse($plain)->startOfDay();
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $date = $value instanceof \DateTimeInterface
            ? Carbon::instance(\DateTimeImmutable::createFromInterface($value))
            : Carbon::parse((string) $value);

        return PiiCrypto::encryptString($date->toDateString());
    }
}
