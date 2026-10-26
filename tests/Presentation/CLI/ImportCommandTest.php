<?php

declare(strict_types=1);

namespace Tests\Presentation\CLI;

use App\Application\ImportTransactions\ImportTransaction;
use App\Application\ImportTransactions\ImportTransactions;
use App\Application\ImportTransactions\SaveImportedTransaction;
use App\Application\ImportTransactions\TransactionInputValidator;
use App\Infrastructure\Input\JsonTransactionFileReader;
use App\Infrastructure\Logging\LoggerFactory;
use App\Presentation\CLI\ImportCommand;
use PHPUnit\Framework\TestCase;

final class ImportCommandTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'transaction-import-');

        if ($filePath === false) {
            self::fail('Cannot create temporary file');
        }

        $this->filePath = $filePath;
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function testSingleTransactionIsImported(): void
    {
        file_put_contents($this->filePath, json_encode([
            'id' => 'trx-001',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'EUR',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ], JSON_THROW_ON_ERROR));

        $command = $this->createCommand();

        ob_start();
        $exitCode = $command->execute($this->filePath);
        $output = ob_get_clean();

        self::assertSame(0, $exitCode);
        self::assertSame('Transaction imported: trx-001' . PHP_EOL, $output);
    }

    private function createCommand(): ImportCommand
    {
        $importTransaction = new ImportTransaction(new TransactionInputValidator());
        $saveImportedTransaction = $this->createStub(SaveImportedTransaction::class);

        return new ImportCommand(
            new JsonTransactionFileReader(),
            $importTransaction,
            new ImportTransactions($importTransaction),
            $saveImportedTransaction,
            LoggerFactory::create($this->filePath . '.log'),
        );
    }
}
