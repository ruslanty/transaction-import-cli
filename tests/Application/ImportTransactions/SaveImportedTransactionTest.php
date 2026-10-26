<?php

declare(strict_types=1);

namespace Tests\Application\ImportTransactions;

use App\Application\ImportTransactions\SaveImportedTransaction;
use App\Domain\Transaction\Money;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SaveImportedTransactionTest extends TestCase
{
    public function testTransactionIsSaved(): void
    {
        $repository = new InMemoryTransactionRepository();
        $service = new SaveImportedTransaction($repository);
        $transaction = $this->createTransaction();

        $service->execute($transaction);

        self::assertSame($transaction, $repository->savedTransaction);
    }

    public function testDuplicateExternalIdIsRejected(): void
    {
        $repository = new InMemoryTransactionRepository();
        $repository->existingExternalId = 'external-001';
        $service = new SaveImportedTransaction($repository);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/already exists/');

        $service->execute($this->createTransaction());
    }

    private function createTransaction(): Transaction
    {
        return new Transaction(
            id: 'trx-001',
            externalId: 'external-001',
            amount: Money::fromDecimal('125.50', 'EUR'),
            occurredAt: new DateTimeImmutable('2026-10-01T12:00:00+03:00'),
        );
    }
}

final class InMemoryTransactionRepository implements TransactionRepository
{
    public ?Transaction $savedTransaction = null;
    public ?string $existingExternalId = null;

    public function save(Transaction $transaction): void
    {
        $this->savedTransaction = $transaction;
    }

    public function existsByExternalId(string $externalId): bool
    {
        return $this->existingExternalId === $externalId;
    }

    /**
     * @param list<Transaction> $transactions
     */
    public function saveMany(array $transactions): void
    {
        foreach ($transactions as $transaction) {
            $this->save($transaction);
        }
    }
}
