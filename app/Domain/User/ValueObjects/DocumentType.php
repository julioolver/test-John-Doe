<?php

namespace App\Domain\User\ValueObjects;

enum DocumentType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';

    public function isCpf(): bool
    {
        return $this === self::CPF;
    }

    public function isCnpj(): bool
    {
        return $this === self::CNPJ;
    }
}
