<?php

declare(strict_types=1);

namespace App\Application\Wallet\Contracts;

use App\Domain\Wallet\Entity\Wallet;
use App\Domain\Shared\ValueObjects\Money;

interface WalletRepository
{
    public function getByUserId(int $userId): Wallet;

    public function getByUserIdForUpdate(int $userId): Wallet;

    public function updateBalance(int $userId, Money $amount): bool;
}
