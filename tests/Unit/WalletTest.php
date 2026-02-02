<?php

namespace Tests\Unit;

use App\Domain\Shared\ValueObjects\Money;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;
use App\Domain\Wallet\Entity\Wallet;
use App\Domain\Wallet\Exception\InsufficientBalanceException;
use App\Domain\Wallet\Exception\InvalidAmountException;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    public function testWalletCanBeCreated(): void
    {
        $wallet = new Wallet(Money::fromCents(1000), $this->makeUser('1'));

        $this->assertSame(1000, $wallet->balance->cents());
    }

    public function testWalletCanDeposit(): void
    {
        $wallet = new Wallet(Money::fromCents(1000), $this->makeUser('1'));
        $wallet->deposit(Money::fromCents(500));

        $this->assertSame(1500, $wallet->balance->cents());
    }

    public function testWalletCanWithdraw(): void
    {
        $wallet = new Wallet(Money::fromCents(1000), $this->makeUser('1'));
        $wallet->withdraw(Money::fromCents(500));

        $this->assertSame(500, $wallet->balance->cents());
    }

    public function testWalletCanTransfer(): void
    {
        $wallet1 = new Wallet(Money::fromCents(1000), $this->makeUser('1'));
        $wallet2 = new Wallet(Money::fromCents(0), $this->makeUser('2'));
        $wallet1->transfer($wallet2, Money::fromCents(500));

        $this->assertSame(500, $wallet1->balance->cents());
        $this->assertSame(500, $wallet2->balance->cents());
    }

    public function testWalletCannotTransferMoreThanItHas(): void
    {
        $walletFrom = new Wallet(Money::fromCents(1000), $this->makeUser('1'));
        $walletTo = new Wallet(Money::fromCents(0), $this->makeUser('2'));

        $this->expectException(InsufficientBalanceException::class);

        $walletFrom->transfer($walletTo, Money::fromCents(1500));
    }

    public function testWalletCannotDepositNegativeAmount(): void
    {
        $wallet = new Wallet(Money::fromCents(1000), $this->makeUser('1'));

        $this->expectException(InvalidAmountException::class);

        $wallet->deposit(Money::fromCents(-500));
    }

    public function testWalletCannotWithdrawNegativeAmount(): void
    {
        $wallet = new Wallet(Money::fromCents(1000), $this->makeUser('1'));

        $this->expectException(InvalidAmountException::class);

        $wallet->withdraw(Money::fromCents(-500));
    }

    private function makeUser(string $id): User
    {
        return new User(
            name: 'User '.$id,
            email: 'user'.$id.'@example.com',
            document: Document::from('12345678901', DocumentType::CPF),
            id: $id
        );
    }
}
