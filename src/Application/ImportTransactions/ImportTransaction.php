<?php

declare(strict_types=1);

namespace App\Application\ImportTransactions;

use App\Domain\Transaction\Money;
use App\Domain\Transaction\Transaction;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

final readonly class ImportTransaction
{
    public function __construct(
        private TransactionInputValidator $validator,
    ) {}

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): Transaction
    {
        $this->validator->validate($input);

        if (!is_string($input['id'])
            || !is_string($input['external_id'])
            || !is_string($input['amount'])
            || !is_string($input['currency'])
            || !is_string($input['occurred_at'])
        ) {
            throw new InvalidArgumentException('Invalid transaction input');
        }

        try {
            $occurredAt = new DateTimeImmutable($input['occurred_at'], new DateTimeZone('UTC'));
        } catch (Exception $exception) {
            throw new InvalidArgumentException('Transaction date must be valid', previous: $exception);
        }

        return new Transaction(
            id: $input['id'],
            externalId: $input['external_id'],
            amount: Money::fromDecimal($input['amount'], $input['currency']),
            occurredAt: $occurredAt,
        );
    }
}
