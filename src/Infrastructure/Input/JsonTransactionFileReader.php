<?php

declare(strict_types=1);

namespace App\Infrastructure\Input;

use JsonException;
use RuntimeException;

final class JsonTransactionFileReader
{
    /**
     * @return array<array-key, mixed>
     */
    public function read(string $path): array
    {
        if (!is_file($path)
            || !is_readable($path)
        ) {
            throw new RuntimeException(sprintf('Cannot read input file: %s', $path));
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException(sprintf('Cannot read input file: %s', $path));
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException(
                sprintf('Invalid JSON in input file: %s', $path),
                previous: $exception,
            );
        }

        if (!is_array($data)) {
            throw new RuntimeException('Input JSON must contain an object or an array');
        }

        return $data;
    }
}
