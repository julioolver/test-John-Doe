<?php

declare(strict_types=1);

namespace App\Application\Transfer\DTOs;

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Wallet\Entity\Wallet;

class TransferInputDTO
{
    public function __construct(
        public Wallet $payer,
        public Wallet $payee,
        public Money $amount,
        public \DateTime $createdAt = new \DateTime(),
    ) {}
}
