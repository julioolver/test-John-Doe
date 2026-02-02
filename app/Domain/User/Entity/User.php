<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Shared\Exceptions\UnauthorizedPayerException;
use App\Domain\User\ValueObjects\Document;

class User
{
    public function __construct(
        public string $name,
        public string $email,
        public Document $document,
        public ?string $id = null,
        public ?string $password = null,
    ) {}

    public function isMerchant(): bool
    {
        return $this->document->type()->isCnpj();
    }

    public function assertCanTransfer(): void
    {
        if ($this->isMerchant()) {
            throw new UnauthorizedPayerException('Merchant cannot transfer money.');
        }
    }
}
