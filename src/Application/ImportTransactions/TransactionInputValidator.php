<?php

declare(strict_types=1);

namespace App\Application\ImportTransactions;

use InvalidArgumentException;

final class TransactionInputValidator
{
    /** @param array<string, mixed> $input */
    public function validate(array $input): void
    {
        if (!isset($input['id'])
            || !is_string($input['id'])
            || trim($input['id']) === ''
        ) {
            throw new InvalidArgumentException('Transaction ID is required');
        }

        if (!isset($input['external_id'])
            || !is_string($input['external_id'])
            || trim($input['external_id']) === ''
        ) {
            throw new InvalidArgumentException('External transaction ID is required');
        }

        if (!isset($input['amount'])
            || !is_string($input['amount'])
            || trim($input['amount']) === ''
        ) {
            throw new InvalidArgumentException('Transaction amount is required');
        }

        if (!preg_match('#^-?\d+(\.\d{1,2})?$#', $input['amount'])) {
            throw new InvalidArgumentException('Transaction amount must be a valid decimal number');
        }

        if (!isset($input['currency'])
            || !is_string($input['currency'])
            || !preg_match('#^[A-Z]{3}$#', $input['currency'])
        ) {
            throw new InvalidArgumentException('Transaction currency must be a three-letter uppercase code');
        }

        if (!isset($input['occurred_at'])
            || !is_string($input['occurred_at'])
            || trim($input['occurred_at']) === ''
        ) {
            throw new InvalidArgumentException('Transaction date is required');
        }
    }
}
