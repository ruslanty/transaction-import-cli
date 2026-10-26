<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use RuntimeException;
use Throwable;

final class DuplicateExternalId extends RuntimeException
{
    public static function for(string $externalId, ?Throwable $previous = null): self
    {
        return new self(
            sprintf('Transaction with external ID "%s" already exists', $externalId),
            previous: $previous,
        );
    }
}
