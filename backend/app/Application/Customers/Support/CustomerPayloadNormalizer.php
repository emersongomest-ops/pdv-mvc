<?php

declare(strict_types=1);

namespace App\Application\Customers\Support;

use App\Domain\Customers\Exceptions\CustomerDomainException;
use App\Domain\Shared\ErrorCode;

final class CustomerPayloadNormalizer
{
    /**
     * @param array<string, mixed> $data
     * @return array{
     *     name: string,
     *     email: string,
     *     cpf: string,
     *     phone: string,
     *     birth_date: string,
     *     address: string
     * }
     */
    public static function normalizeCreate(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'cpf' => self::digitsOnly((string) ($data['cpf'] ?? '')),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'birth_date' => (string) ($data['birth_date'] ?? ''),
            'address' => trim((string) ($data['address'] ?? '')),
        ];
    }

    public static function digitsOnly(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     cpf: string,
     *     phone: string,
     *     birth_date: string,
     *     address: string
     * } $data
     */
    public static function assertRequiredFieldsPresent(array $data): void
    {
        foreach (['name', 'email', 'cpf', 'phone', 'birth_date', 'address'] as $field) {
            if ($data[$field] === '') {
                throw new CustomerDomainException(ErrorCode::CustRequiredFieldMissing);
            }
        }
    }
}
