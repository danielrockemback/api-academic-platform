<?php

declare(strict_types=1);

namespace App\Domain\Student;


use App\Domain\Shared\ValueObject\Email;

interface StudentRepositoryInterface
{
    public function save(Student $student): void;

    public function findById(StudentId $id): ?Student;

    public function findByEmail(Email $email): ?Student;

    public function existsByEmail(Email $email): bool;
}
