<?php

declare(strict_types=1);

namespace Tests\Domain\Transaction;

use App\Domain\Transaction\Money;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionStatus;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testItStartsWithImportedStatus(): void
    {
        $transaction = new Transaction(
            'transaction-1',
            'external-1',
            Money::fromDecimal('10.00', 'EUR'),
            new DateTimeImmutable('2026-01-01'),
        );

        self::assertSame(TransactionStatus::Imported, $transaction->status());
    }

    public function testItCanBeMarkedAsFailed(): void
    {
        $transaction = new Transaction(
            'transaction-1',
            'external-1',
            Money::fromDecimal('10.00', 'EUR'),
            new DateTimeImmutable('2026-01-01'),
        );

        $transaction->markAsFailed();

        self::assertSame(TransactionStatus::Failed, $transaction->status());
    }
}
