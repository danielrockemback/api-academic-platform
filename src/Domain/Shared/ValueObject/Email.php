<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

final class Email
{
    const ERROR_MESSAGE = ' não é um endereço de email válido.';

    public function __construct(private string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf("%s" . self::ERROR_MESSAGE, $value));
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
