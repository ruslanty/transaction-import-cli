<?php

declare(strict_types=1);

namespace Tests\Application\ImportTransactions;

use App\Application\ImportTransactions\TransactionInputValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TransactionInputValidatorTest extends TestCase
{
    public function testValidInputPasses(): void
    {
        $validator = new TransactionInputValidator();

        $this->expectNotToPerformAssertions();

        $validator->validate([
            'id' => 'trx-001',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'EUR',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ]);
    }

    public function testEmptyIdIsRejected(): void
    {
        $validator = new TransactionInputValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Transaction ID is required/');

        $validator->validate([
            'id' => '',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'EUR',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ]);
    }

    public function testInvalidAmountIsRejected(): void
    {
        $validator = new TransactionInputValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Transaction amount must be a valid decimal number/');

        $validator->validate([
            'id' => 'trx-001',
            'external_id' => 'external-001',
            'amount' => '12.999',
            'currency' => 'EUR',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ]);
    }

    public function testInvalidCurrencyIsRejected(): void
    {
        $validator = new TransactionInputValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Transaction currency must be a three-letter uppercase code/');

        $validator->validate([
            'id' => 'trx-001',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'eur',
            'occurred_at' => '2026-10-01T12:00:00+03:00',
        ]);
    }

    public function testMissingDateIsRejected(): void
    {
        $validator = new TransactionInputValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Transaction date is required/');

        $validator->validate([
            'id' => 'trx-001',
            'external_id' => 'external-001',
            'amount' => '125.50',
            'currency' => 'EUR',
        ]);
    }
}
