<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public static function create(string $logPath): LoggerInterface
    {
        $logger = new Logger('transaction-import');
        $logger->pushHandler(new StreamHandler($logPath, Level::Info));

        return $logger;
    }
}
