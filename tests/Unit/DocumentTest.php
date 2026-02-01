<?php

namespace Tests\Unit;

use App\Domain\Shared\Exceptions\InvalidDocumentException;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testCpfWith11DigitsIsValid(): void
    {
        $document = Document::from('123.456.789-01', DocumentType::CPF);

        $this->assertSame('12345678901', $document->value());
        $this->assertTrue($document->type()->isCpf());
    }

    public function testCnpjWithInvalidLengthThrows(): void
    {
        $this->expectException(InvalidDocumentException::class);

        Document::from('1234567890123', DocumentType::CNPJ);
    }
}
