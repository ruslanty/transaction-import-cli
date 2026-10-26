<?php

declare(strict_types=1);

namespace App\Application\ImportTransactions;

use App\Domain\Transaction\Transaction;
use InvalidArgumentException;

final readonly class ImportTransactions
{
    public function __construct(
        private ImportTransaction $importTransaction,
    ) {}

    /**
     * @param array<int, mixed> $input
     *
     * @return list<Transaction>
     */
    public function execute(array $input): array
    {
        $transactions = [];

        foreach ($input as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Each transaction must be an object');
            }

            /** @var array<string, mixed> $item */
            $transactions[] = $this->importTransaction->execute($item);
        }

        return $transactions;
    }
}
