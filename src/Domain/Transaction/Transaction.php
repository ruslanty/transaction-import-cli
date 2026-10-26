<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use DateTimeImmutable;
use InvalidArgumentException;

final class Transaction
{
    public function __construct(
        private readonly string $id,
        private readonly string $externalId,
        private readonly Money $amount,
        private readonly DateTimeImmutable $occurredAt,
        private TransactionStatus $status = TransactionStatus::Imported,
    ) {
        if ($this->id === '') {
            throw new InvalidArgumentException('Transaction ID cannot be empty');
        }

        if ($this->externalId === '') {
            throw new InvalidArgumentException('External transaction ID cannot be empty');
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function externalId(): string
    {
        return $this->externalId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function status(): TransactionStatus
    {
        return $this->status;
    }

    public function markAsFailed(): void
    {
        $this->status = TransactionStatus::Failed;
    }
}
