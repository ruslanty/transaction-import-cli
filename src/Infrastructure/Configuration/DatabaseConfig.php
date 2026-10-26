<?php

declare(strict_types=1);

namespace App\Infrastructure\Configuration;

use RuntimeException;

final readonly class DatabaseConfig
{
    public function __construct(
        public string $host,
        public int $port,
        public string $database,
        public string $username,
        public string $password,
    ) {}

    public static function fromEnvironment(): self
    {
        return new self(
            host: self::required('DB_HOST'),
            port: (int) self::required('DB_PORT'),
            database: self::required('DB_DATABASE'),
            username: self::required('DB_USERNAME'),
            password: self::required('DB_PASSWORD'),
        );
    }

    private static function required(string $name): string
    {
        $value = getenv($name);

        if ($value === false
            || $value === ''
        ) {
            throw new RuntimeException(sprintf('Environment variable %s is required', $name));
        }

        return $value;
    }
}
