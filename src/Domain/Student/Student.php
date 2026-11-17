<?php

declare(strict_types=1);

namespace App\Domain\Student;

use App\Domain\Shared\ValueObject\BirthDate;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\FullName;

final class Student
{
    private const MINIMUM_AGE_ALLOWED = 16;
    private const MAJORITY_AGE = 18;


    public function __construct(
        private readonly ?StudentId $id,
        private FullName $fullName,
        private Email $email,
        private BirthDate $birthDate,
    ) {

        $this->validate();
    }

    private function validate(): void
    {
        if ($this->getBirthDate()->age() < self::MINIMUM_AGE_ALLOWED) {
            throw new \InvalidArgumentException(
                'Não são permitidos alunos com menos de ' . self::MINIMUM_AGE_ALLOWED . ' anos.'
            );
        }
    }

    public function getId(): ?StudentId
    {
        return $this->id;
    }

    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getBirthDate(): BirthDate
    {
        return $this->birthDate;
    }

    public function isMinor(): bool
    {
        return $this->getBirthDate()->age() < self::MAJORITY_AGE;
    }

}
