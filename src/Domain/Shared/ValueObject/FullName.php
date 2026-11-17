<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

final class FullName
{
    public const MIN_LENGTH = 5;
    public const MAX_LENGTH = 255;
    public const MIN_NAME_PARTS = 2;

    public function __construct(private readonly string $value)
    {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('O nome não pode ser vazio.');
        }

        if ($value !== trim($value)) {
            throw new \InvalidArgumentException('O nome não pode começar ou terminar com espaços.');
        }

        if (!preg_match('/^[\p{L} ]+$/u', $value)) {
            throw new \InvalidArgumentException('O nome completo não pode ter números.');
        }

        $totalCharacters = mb_strlen($value);

        if ($totalCharacters < self::MIN_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('O nome deve ter pelo menos %d caracteres.', self::MIN_LENGTH)
            );
        }

        if ($totalCharacters > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('O nome deve ter no máximo %d caracteres.', self::MAX_LENGTH)
            );
        }

        if (count(preg_split('/\s+/', $value)) < self::MIN_NAME_PARTS) {
            throw new \InvalidArgumentException('Digite o seu nome completo.');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(FullName $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
