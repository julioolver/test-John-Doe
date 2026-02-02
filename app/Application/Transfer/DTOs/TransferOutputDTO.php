<?php

declare(strict_types=1);

namespace App\Application\Transfer\DTOs;

use App\Domain\Transfer\Entity\Transfer;
use JsonSerializable;

class TransferOutputDTO implements JsonSerializable
{
    public function __construct(
        public Transfer $transfer,
    ) {}

    /**
     * @return array<string, string|null>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->transfer->id,
            'status' => $this->transfer->status->value,
            'value' => $this->transfer->amount->toDecimal(),
            'payer' => $this->transfer->payer->user->id,
            'payee' => $this->transfer->payee->user->id,
            'created_at' => $this->transfer->createdAt->format(DATE_ATOM),
        ];
    }
}
