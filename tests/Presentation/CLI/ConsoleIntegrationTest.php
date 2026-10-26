<?php

declare(strict_types=1);

namespace Tests\Presentation\CLI;

use PHPUnit\Framework\TestCase;

final class ConsoleIntegrationTest extends TestCase
{
    public function testInvalidInputReturnsErrorExitCode(): void
    {
        $process = proc_open(
            [PHP_BINARY, 'bin/console', 'import', 'var/input-invalid.json'],
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            dirname(__DIR__, 3),
            [
                'DB_HOST' => 'mysql',
                'DB_PORT' => '3306',
                'DB_DATABASE' => 'transaction_import',
                'DB_USERNAME' => 'transaction_import',
                'DB_PASSWORD' => 'transaction_import',
            ],
        );

        self::assertIsResource($process);

        $output = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString(
            'Transaction amount must be a valid decimal number',
            $output . $errorOutput,
        );
    }
}
