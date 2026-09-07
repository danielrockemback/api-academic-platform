<?php

namespace App\Domain\Student;

final class StudentId
{
    public function __construct(private readonly int $value)
    {
        $this->validate($value);
    }

    private function validate(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('O ID do aluno(a) dever ser um número positivo.');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(StudentId $other): bool
    {
        return $this->value === $other->value;
    }
}
