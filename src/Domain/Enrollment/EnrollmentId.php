<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

final class EnrollmentId
{
    public function __construct(private readonly int $value)
    {
        $this->validate($value);
    }

    private function validate($value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('O ID da inscrição dever ser um número positivo.');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(EnrollmentId $other): bool
    {
        return $other->getValue() === $this->getValue();
    }
}
