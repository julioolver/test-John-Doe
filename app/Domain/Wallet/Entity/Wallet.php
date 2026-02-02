<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Entity;

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Wallet\Exception\InvalidAmountException;
use App\Domain\Wallet\Exception\InsufficientBalanceException;

class Wallet
{
    public function __construct(
        public Money $balance,
    ) {}

    public function deposit(Money $amount): void
    {
        $this->ensurePositiveAmount($amount);
        $this->balance = $this->balance->add($amount);
    }

    public function withdraw(Money $amount): void
    {
        $this->ensurePositiveAmount($amount);
        $this->balance = $this->balance->subtract($amount);
    }

    public function transfer(Wallet $to, Money $amount): void
    {
        $this->ensureHasBalance($amount);

        $this->withdraw($amount);
        $to->deposit($amount);
    }

    public function ensureHasBalance(Money $amount): void
    {
        $this->ensurePositiveAmount($amount);

        if (! $this->balance->greaterThanOrEqual($amount)) {
            throw new InsufficientBalanceException('Insufficient balance to transfer.');
        }
    }

    private function ensurePositiveAmount(Money $amount): void
    {
        if (! $amount->isPositive()) {
            throw new InvalidAmountException('Amount must be greater than zero.');
        }
    }
}
