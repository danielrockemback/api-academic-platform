<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

final class RegistrationNumber
{
    private const LENGTH_REGISTRATION_NUMBER = 10;
    private const FORMART_REGISTRATION_NUMBER = 'AAAA-NNNNN';

    public function __construct(private readonly string $value)
    {
        $this->validate($value);
    }

    private function validate(string $value)
    {
        $formatRegistrationNumber = self::FORMART_REGISTRATION_NUMBER;
        $value = trim($value);
        $totalCharacters = mb_strlen($value);

        if (empty($value)) {
            throw new \InvalidArgumentException('O número de registro não pode ser vazio');
        }

        if (!str_contains($value, '-')) {
            throw new \InvalidArgumentException("O número de registro deve conter um hífen. {$formatRegistrationNumber}");
        }

        if ($totalCharacters != self::LENGTH_REGISTRATION_NUMBER) {
            throw new \InvalidArgumentException("O número de registro tem que ter 10 caracteres. {$formatRegistrationNumber}");
        }

        [$yearRegistrationNumber, $sequencialNumber] = (explode('-', $value));
        $currentYear = (int) date('Y');

        if (!is_numeric($yearRegistrationNumber)) {
            throw new \InvalidArgumentException('Os 4 primeiros caracteres devem ser números.');
        }

        if ((int) $yearRegistrationNumber !== $currentYear) {
            throw new \InvalidArgumentException('Os 4 primeiros caracteres devem ser representado pelo ano vigente.');
        }

        if ((int) $sequencialNumber <= 0) {
            throw new \InvalidArgumentException('O número sequencial caracteres deve ser maior que 0.');
        }

    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(RegistrationNumber $other): bool
    {
        return $this->getValue() === $other->getValue();
    }
}
