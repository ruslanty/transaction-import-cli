<?php

declare(strict_types=1);

namespace App\Presentation\CLI;

use App\Application\ImportTransactions\ImportTransaction;
use App\Application\ImportTransactions\ImportTransactions;
use App\Application\ImportTransactions\SaveImportedTransaction;
use App\Infrastructure\Input\JsonTransactionFileReader;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class ImportCommand
{
    public function __construct(
        private JsonTransactionFileReader $reader,
        private ImportTransaction $importTransaction,
        private ImportTransactions $importTransactions,
        private SaveImportedTransaction $saveImportedTransaction,
        private LoggerInterface $logger,
    ) {}

    public function execute(string $inputFile): int
    {
        try {
            $input = $this->reader->read($inputFile);

            if (array_is_list($input)) {
                /** @var array<int, mixed> $input */
                $transactions = $this->importTransactions->execute($input);

                $this->saveImportedTransaction->executeMany($transactions);

                $this->logger->info('Transactions imported', [
                    'count' => count($transactions),
                    'file' => $inputFile,
                ]);

                echo 'Transactions imported: ', count($transactions), PHP_EOL;
            } else {
                /** @var array<string, mixed> $input */
                $transaction = $this->importTransaction->execute($input);
                $this->saveImportedTransaction->execute($transaction);

                $this->logger->info('Transaction imported', [
                    'id' => $transaction->id(),
                    'file' => $inputFile,
                ]);

                echo 'Transaction imported: ', $transaction->id(), PHP_EOL;
            }

            return 0;
        } catch (Throwable $exception) {
            $this->logger->error('Transaction import failed', [
                'file' => $inputFile,
                'error' => $exception->getMessage(),
            ]);

            echo 'Error: ', $exception->getMessage(), PHP_EOL;

            return 1;
        }
    }
}
