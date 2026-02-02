<?php

declare(strict_types=1);

namespace App\Domain\Transfer\Entity;

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Wallet\Entity\Wallet;
use App\Domain\Transfer\Enums\TransferStatus;

class Transfer
{
    public function __construct(
        public Wallet $payer,
        public Wallet $payee,
        public Money $amount,
        public TransferStatus $status = TransferStatus::PENDING,
        public \DateTime $createdAt = new \DateTime(),
        public ?string $id = null,
    ) {}

    public function execute(): void
    {
        $this->payer->ensureHasBalance($this->amount);

        $this->payer->withdraw($this->amount);
        $this->payee->deposit($this->amount);

        $this->status = TransferStatus::SUCCESS;
    }

    public function cancel(): void
    {
        $this->status = TransferStatus::CANCELLED;
    }
}
