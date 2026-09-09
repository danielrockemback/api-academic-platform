<?php

declare(strict_types=1);

namespace App\Domain\Student;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\FullName;

final class Student
{
    public function __construct(
        private readonly ?StudentId $id,
        private FullName $fullName,
        private Email $email
    ) {
        //$this->validateName();
    }

    private function validateName(string $name)
    {
        //if ()
    }
}
