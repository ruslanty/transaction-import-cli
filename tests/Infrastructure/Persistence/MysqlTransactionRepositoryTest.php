<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence;

use App\Domain\Transaction\DuplicateExternalId;
use App\Domain\Transaction\Money;
use App\Domain\Transaction\Transaction;
use App\Infrastructure\Persistence\MysqlTransactionRepository;
use App\Infrastructure\Persistence\PdoConnectionFactory;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\TestCase;

final class MysqlTransactionRepositoryTest extends TestCase
{
    private PDO $connection;
    private string $externalId;

    protected function setUp(): void
    {
        $this->connection = PdoConnectionFactory::create(
            'mysql',
            3306,
            'transaction_import',
            'transaction_import',
            'transaction_import',
        );

        $this->externalId = 'external-' . bin2hex(random_bytes(8));
    }

    protected function tearDown(): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM transactions WHERE external_id = :external_id',
        );
        $statement->execute(['external_id' => $this->externalId]);
    }

    public function testTransactionCanBeSavedAndFound(): void
    {
        $repository = new MysqlTransactionRepository($this->connection);
        $transaction = new Transaction(
            id: 'trx-' . bin2hex(random_bytes(8)),
            externalId: $this->externalId,
            amount: Money::fromDecimal('125.50', 'EUR'),
            occurredAt: new DateTimeImmutable('2026-10-01T12:00:00+03:00'),
        );

        self::assertFalse($repository->existsByExternalId($this->externalId));

        $repository->save($transaction);

        self::assertTrue($repository->existsByExternalId($this->externalId));
    }

    public function testDuplicateExternalIdIsConvertedToDomainException(): void
    {
        $repository = new MysqlTransactionRepository($this->connection);
        $firstTransaction = new Transaction(
            id: 'trx-' . bin2hex(random_bytes(8)),
            externalId: $this->externalId,
            amount: Money::fromDecimal('10.00', 'EUR'),
            occurredAt: new DateTimeImmutable('2026-10-01T12:00:00+03:00'),
        );
        $secondTransaction = new Transaction(
            id: 'trx-' . bin2hex(random_bytes(8)),
            externalId: $this->externalId,
            amount: Money::fromDecimal('20.00', 'EUR'),
            occurredAt: new DateTimeImmutable('2026-10-01T13:00:00+03:00'),
        );

        $repository->save($firstTransaction);

        $this->expectException(DuplicateExternalId::class);
        $this->expectExceptionMessageIs(
            sprintf('Transaction with external ID "%s" already exists', $this->externalId),
        );

        $repository->save($secondTransaction);
    }
}
