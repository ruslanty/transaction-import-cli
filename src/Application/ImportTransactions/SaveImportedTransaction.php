<?php

declare(strict_types=1);

namespace App\Application\ImportTransactions;

use App\Domain\Transaction\DuplicateExternalId;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use InvalidArgumentException;

readonly class SaveImportedTransaction
{
    public function __construct(
        private TransactionRepository $repository,
    ) {}

    public function execute(Transaction $transaction): void
    {
        if ($this->repository->existsByExternalId($transaction->externalId())) {
            throw new InvalidArgumentException(
                sprintf(
                    'Transaction with external ID "%s" already exists',
                    $transaction->externalId(),
                ),
            );
        }

        $this->repository->save($transaction);
    }

    /**
     * @param list<Transaction> $transactions
     */
    public function executeMany(array $transactions): void
    {
        foreach ($transactions as $transaction) {
            if ($this->repository->existsByExternalId($transaction->externalId())) {
                throw DuplicateExternalId::for($transaction->externalId());
            }
        }

        $this->repository->saveMany($transactions);
    }
}
