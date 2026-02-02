<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Entity;

use App\Domain\Wallet\Exception\InsufficientBalanceException;

class Wallet
{
    public function __construct(
        public int $balance,
        public bool $canTransfer,
    ) {}

    public function deposit(int $amount): void
    {
        $this->balance += $amount;
    }

    public function withdraw(int $amount): void
    {
        $this->balance -= $amount;
    }

    public function transfer(Wallet $to, int $amount): void
    {
        $this->ensureHasBalance($amount);

        $this->withdraw($amount);
        $to->deposit($amount);
    }

    public function ensureHasBalance(int $amount): void
    {
        if ($this->balance < $amount) {
            throw new InsufficientBalanceException('Insufficient balance to transfer.');
        }
    }
}
