<?php

namespace App\Domain\User\ValueObjects;

enum DocumentType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';
}
