<?php

declare(strict_types=1);

namespace App\Application\Transfer\Contracts;

use App\Domain\Transfer\Entity\Transfer;

interface TransferRepository
{
    public function create(Transfer $transfer): Transfer;

    public function findById(string $id): ?Transfer;
}
