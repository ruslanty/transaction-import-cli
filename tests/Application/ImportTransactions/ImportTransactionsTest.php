<?php

declare(strict_types=1);

namespace Tests\Application\ImportTransactions;

use App\Application\ImportTransactions\ImportTransaction;
use App\Application\ImportTransactions\ImportTransactions;
use App\Application\ImportTransactions\TransactionInputValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ImportTransactionsTest extends TestCase
{
    public function testSeveralTransactionsAreImported(): void
    {
        $importTransaction = new ImportTransaction(new TransactionInputValidator());
        $useCase = new ImportTransactions($importTransaction);

        $transactions = $useCase->execute([
            [
                'id' => 'trx-001',
                'external_id' => 'external-001',
                'amount' => '125.50',
                'currency' => 'EUR',
                'occurred_at' => '2026-10-01T12:00:00+03:00',
            ],
            [
                'id' => 'trx-002',
                'external_id' => 'external-002',
                'amount' => '20.00',
                'currency' => 'USD',
                'occurred_at' => '2026-10-01T13:00:00+03:00',
            ],
        ]);

        self::assertCount(2, $transactions);
        self::assertSame('trx-001', $transactions[0]->id());
        self::assertSame('trx-002', $transactions[1]->id());
    }

    public function testInvalidTransactionStopsTheBatch(): void
    {
        $importTransaction = new ImportTransaction(new TransactionInputValidator());
        $useCase = new ImportTransactions($importTransaction);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Transaction amount must be a valid decimal number/');

        $useCase->execute([
            [
                'id' => 'trx-004',
                'external_id' => 'external-004',
                'amount' => '10.00',
                'currency' => 'EUR',
                'occurred_at' => '2026-10-01T16:00:00+03:00',
            ],
            [
                'id' => 'trx-invalid-002',
                'external_id' => 'external-invalid-002',
                'amount' => 'wrong',
                'currency' => 'EUR',
                'occurred_at' => '2026-10-01T16:05:00+03:00',
            ],
        ]);
    }

    public function testNonArrayTransactionIsRejected(): void
    {
        $importTransaction = new ImportTransaction(new TransactionInputValidator());
        $useCase = new ImportTransactions($importTransaction);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Each transaction must be an object/');

        /** @var array<int, mixed> $input */
        $input = ['invalid transaction'];
        $useCase->execute($input);
    }
}
