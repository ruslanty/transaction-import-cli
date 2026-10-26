<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Transaction\DuplicateExternalId;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use DateTimeImmutable;
use PDO;
use PDOException;
use Throwable;

final readonly class MysqlTransactionRepository implements TransactionRepository
{
    public function __construct(
        private PDO $connection,
    ) {}

    /**
     * @throws \Throwable
     */
    public function save(Transaction $transaction): void
    {
        $this->saveMany([$transaction]);
    }

    /**
     * @param list<Transaction> $transactions
     *
     * @throws \Throwable
     */
    public function saveMany(array $transactions): void
    {
        $this->connection->beginTransaction();

        try {
            foreach ($transactions as $transaction) {
                $this->insert($transaction);
            }

            $this->connection->commit();
        } catch (Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }

    public function existsByExternalId(string $externalId): bool
    {
        $statement = $this->connection->prepare(
            'SELECT 1 FROM transactions WHERE external_id = :external_id LIMIT 1',
        );
        $statement->execute(['external_id' => $externalId]);

        return $statement->fetchColumn() !== false;
    }

    private function insert(Transaction $transaction): void
    {
        $statement = $this->connection->prepare(
            <<<'SQL'
            INSERT INTO transactions (
                id,
                external_id,
                amount_minor_units,
                currency,
                occurred_at,
                status,
                created_at
            ) VALUES (
                :id,
                :external_id,
                :amount_minor_units,
                :currency,
                :occurred_at,
                :status,
                :created_at
            )
            SQL,
        );

        try {
            $statement->execute([
                'id' => $transaction->id(),
                'external_id' => $transaction->externalId(),
                'amount_minor_units' => $transaction->amount()->minorUnits,
                'currency' => $transaction->amount()->currency,
                'occurred_at' => $transaction->occurredAt()->format('Y-m-d H:i:s'),
                'status' => $transaction->status()->value,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1062) {
                throw DuplicateExternalId::for($transaction->externalId(), $exception);
            }

            throw $exception;
        }
    }
}
