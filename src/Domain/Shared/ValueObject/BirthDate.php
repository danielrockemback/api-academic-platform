<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

final class BirthDate
{
    private const MAXIMUM_NUMBER_OF_PAST_YEARS_ALLOWED = 120;

    public function __construct(private readonly string $value)
    {
        $this->validate($value);
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new InvalidArgumentException('A data de nascimento deve ser preenchida.');
        }

        if (!$this->isValidDate($value)) {
            throw new InvalidArgumentException('A data de nascimento está em um formato inválido.');
        }

        $today = new DateTimeImmutable('today');
        $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $dateBack120Years = $today->modify('-' . self::MAXIMUM_NUMBER_OF_PAST_YEARS_ALLOWED . ' years');


        if ($birthDate > $today) {
            throw new InvalidArgumentException(
                'Não é permitido informar uma data de nascimento no futuro.'
            );
        }

        if ($birthDate < $dateBack120Years) {
            throw new InvalidArgumentException('A data de nascimento não pode ultrapassar 120 anos.');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function isValidDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date !== false
            && $date->format('Y-m-d') === $value;
    }

    public function age(): int
    {
        $today = new DateTimeImmutable('today');
        $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $this->getValue());

        return $birthDate->diff($today)->y;
    }

    public function equals(BirthDate $birthDate): bool
    {
        return $this->getValue() === $birthDate->getValue();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
