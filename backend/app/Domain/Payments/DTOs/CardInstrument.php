<?php

declare(strict_types=1);

namespace App\Domain\Payments\DTOs;

final readonly class CardInstrument
{
    public function __construct(
        public string $holderName,
        public string $number,
        public int $expiryMonth,
        public int $expiryYear,
        public string $indicatedPersonName,
        public bool $belongsToIndicatedPerson,
    ) {}

    public function digitsOnlyNumber(): string
    {
        return preg_replace('/\D+/', '', $this->number) ?? '';
    }

    public function normalizedHolderName(): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $this->holderName) ?? ''));
    }

    public function normalizedIndicatedPersonName(): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $this->indicatedPersonName) ?? ''));
    }
}
