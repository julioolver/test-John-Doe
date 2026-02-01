<?php

namespace Tests\Unit;

use App\Domain\Shared\Exceptions\UnauthorizedPayerException;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testMerchantCannotTransfer(): void
    {
        $document = Document::from('12.345.678/0001-90', DocumentType::CNPJ);
        $user = new User(
            name: 'Loja X',
            email: 'loja@example.com',
            document: $document,
        );

        $this->expectException(UnauthorizedPayerException::class);

        $user->assertCanTransfer();
    }

    public function testRegularUserCanTransfer(): void
    {
        $document = Document::from('123.456.789-01', DocumentType::CPF);
        $user = new User(
            name: 'Joao',
            email: 'joao@example.com',
            document: $document,
        );

        $user->assertCanTransfer();

        $this->assertFalse($user->isMerchant());
    }
}
