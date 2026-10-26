<?php

declare(strict_types=1);

namespace Tests\Application\ImportTransactions;

use App\Application\ImportTransactions\ImportTransaction;
use App\Application\ImportTransactions\TransactionInputValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ImportTransactionTest extends TestCase
{
    public function testTransactionIsCreatedFromValidInput(): void
    {
        $useCase = new ImportTransaction(new TransactionInputValidator());

        $transaction = $useCase->execute([
            'id' => 'trx-001',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'EUR',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ]);

        self::assertSame('trx-001', $transaction->id());
        self::assertSame('external-001', $transaction->externalId());
        self::assertSame(12550, $transaction->amount()->minorUnits);
        self::assertSame('EUR', $transaction->amount()->currency);
    }

    public function testInvalidInputIsRejected(): void
    {
        $useCase = new ImportTransaction(new TransactionInputValidator());

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute([
            'id' => '',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'EUR',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ]);
    }
}
