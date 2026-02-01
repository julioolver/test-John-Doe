<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObjects;

use App\Domain\Shared\Exceptions\InvalidDocumentException;

class Document
{
    private function __construct(
        private string $value,
        private DocumentType $type,
    ) {}

    public static function from(string $value, DocumentType $type): self
    {
        $normalized = preg_replace('/\D+/', '', $value) ?? '';
        $length = strlen($normalized);

        if ($type->isCpf() && $length !== 11) {
            throw new InvalidDocumentException('Document must be 11 characters long.');
        }

        if ($type->isCnpj() && $length !== 14) {
            throw new InvalidDocumentException('Document must be 14 characters long.');
        }

        return new self($normalized, $type);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function type(): DocumentType
    {
        return $this->type;
    }
}
