<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

interface TransactionRepository
{
    public function save(Transaction $transaction): void;

    /**
     * @param list<Transaction> $transactions
     */
    public function saveMany(array $transactions): void;

    public function existsByExternalId(string $externalId): bool;
}
