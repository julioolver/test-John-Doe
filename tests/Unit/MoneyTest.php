<?php

namespace Tests\Unit;

use App\Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testMoneyCanBeCreatedFromCents(): void
    {
        $money = Money::fromCents(1000);

        $this->assertSame(1000, $money->cents());
    }

    public function testMoneyCanBeCreatedFromDecimal(): void
    {
        $money = Money::fromDecimal('10.00');

        $this->assertSame(1000, $money->cents());
    }

    public function testMoneyCanBeAdded(): void
    {
        $money1 = Money::fromCents(1000);
        $money2 = Money::fromCents(2000);

        $result = $money1->add($money2);

        $this->assertSame(3000, $result->cents());
    }

    public function testMoneyCanBeSubtracted(): void
    {
        $money1 = Money::fromCents(2000);
        $money2 = Money::fromCents(1000);

        $result = $money1->subtract($money2);

        $this->assertSame(1000, $result->cents());
    }
}
