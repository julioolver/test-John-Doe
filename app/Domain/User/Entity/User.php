<?php

namespace App\Domain\User\Entity;

use App\Domain\Shared\Exceptions\UnauthorizedPayerException;
use App\Domain\User\ValueObjects\DocumentType;
use InvalidArgumentException;

class User
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $password,
        public string $document,
        public DocumentType $documentType,
    ) {}

    public function isMerchant(): bool
    {
        return $this->documentType === DocumentType::CNPJ;
    }

    public function canTransfer(): void
    {
        if ($this->isMerchant()) {
            throw new UnauthorizedPayerException('Merchant cannot transfer money.');
        }
    }

    public function checkDocument(): void
    {
        if ($this->documentType === DocumentType::CPF) {
            if (strlen($this->document) !== 11) {
                throw new InvalidArgumentException('Document must be 11 characters long.');
            }
        }

        if ($this->documentType === DocumentType::CNPJ) {
            if (strlen($this->document) !== 14) {
                throw new InvalidArgumentException('Document must be 14 characters long.');
            }
        }
    }
}
