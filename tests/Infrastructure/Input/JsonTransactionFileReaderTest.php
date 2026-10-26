<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Input;

use App\Infrastructure\Input\JsonTransactionFileReader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class JsonTransactionFileReaderTest extends TestCase
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

    public function testValidJsonIsRead(): void
    {
        file_put_contents($this->filePath, '{"id":"trx-001","amount":"125.50"}');

        $result = (new JsonTransactionFileReader())->read($this->filePath);

        self::assertSame([
            'id' => 'trx-001',
            'amount' => '125.50',
        ], $result);
    }

    public function testMissingFileIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Cannot read input file/');

        (new JsonTransactionFileReader())->read('/tmp/file-that-does-not-exist.json');
    }

    public function testInvalidJsonIsRejected(): void
    {
        file_put_contents($this->filePath, '{invalid json');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Invalid JSON in input file/');

        (new JsonTransactionFileReader())->read($this->filePath);
    }

    public function testScalarJsonIsRejected(): void
    {
        file_put_contents($this->filePath, '"transaction"');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Input JSON must contain an object or an array/');

        (new JsonTransactionFileReader())->read($this->filePath);
    }
}
