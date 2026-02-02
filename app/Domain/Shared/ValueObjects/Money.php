<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

class Money
{
    private function __construct(
        private int $cents,
    ) {}

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public static function fromDecimal(string $amount): self
    {
        $normalized = str_replace(',', '.', trim($amount));

        if (! preg_match('/^-?\d+(\.\d{1,2})?$/', $normalized)) {
            throw new \InvalidArgumentException('Invalid money format.');
        }

        /** @var numeric-string $normalized */
        $cents = (int) bcmul($normalized, '100', 0);

        return new self($cents);
    }

    public function add(self $amount): self
    {
        return new self($this->cents + $amount->cents);
    }

    public function subtract(self $amount): self
    {
        return new self($this->cents - $amount->cents);
    }

    public function greaterThanOrEqual(self $amount): bool
    {
        return $this->cents >= $amount->cents;
    }

    public function isPositive(): bool
    {
        return $this->cents > 0;
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function toDecimal(): string
    {
        $value = $this->cents / 100;

        return number_format($value, 2, '.', '');
    }
}
