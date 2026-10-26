<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $minorUnits,
        public string $currency,
    ) {
        if (!preg_match('#^[A-Z]{3}$#', $this->currency)) {
            throw new InvalidArgumentException('Currency must be ISO code');
        }
    }

    public static function fromDecimal(string $amount, string $currency): self
    {
        if (!preg_match('#^-?\d+(\.\d{1,2})?$#', $amount)) {
            throw new InvalidArgumentException('Invalid money amount');
        }

        $negative = str_starts_with($amount, '-');
        $normalized = ltrim($amount, '-');
        [$whole, $fraction] = array_pad(explode('.', $normalized, 2), 2, '0');
        $minorUnits = ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');

        return new self($negative ? -$minorUnits : $minorUnits, $currency);
    }
}
