<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Configuration;

use App\Infrastructure\Configuration\DatabaseConfig;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DatabaseConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $name) {
            putenv($name);
        }
    }

    public function testConfigurationIsCreatedFromEnvironment(): void
    {
        putenv('DB_HOST=mysql');
        putenv('DB_PORT=3306');
        putenv('DB_DATABASE=transaction_import');
        putenv('DB_USERNAME=transaction_import');
        putenv('DB_PASSWORD=transaction_import');

        $config = DatabaseConfig::fromEnvironment();

        self::assertSame('mysql', $config->host);
        self::assertSame(3306, $config->port);
        self::assertSame('transaction_import', $config->database);
        self::assertSame('transaction_import', $config->username);
        self::assertSame('transaction_import', $config->password);
    }

    public function testMissingEnvironmentVariableIsRejected(): void
    {
        putenv('DB_HOST');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Environment variable DB_HOST is required/');

        DatabaseConfig::fromEnvironment();
    }
}
