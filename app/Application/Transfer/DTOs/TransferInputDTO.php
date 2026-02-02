<?php

declare(strict_types=1);

namespace App\Application\Transfer\DTOs;

use App\Domain\Shared\ValueObjects\Money;

class TransferInputDTO
{
    public function __construct(
        public int $payerId,
        public int $payeeId,
        public Money $amount,
        public \DateTime $createdAt = new \DateTime(),
    ) {}
}
