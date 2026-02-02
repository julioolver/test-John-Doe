<?php

declare(strict_types=1);

namespace App\Application\Transfer\DTOs;

use App\Domain\Transfer\Entity\Transfer;

class TransferOutputDTO
{
    public function __construct(
        public Transfer $transfer,
    ) {}
}
