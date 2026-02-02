<?php

declare(strict_types=1);

namespace App\Application\Wallet\Contracts;

use App\Domain\Wallet\Entity\Wallet;

interface WalletRepository
{
    public function getByUserId(int $userId): Wallet;
}
