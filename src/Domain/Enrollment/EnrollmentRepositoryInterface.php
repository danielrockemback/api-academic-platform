<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

use App\Domain\Student\StudentId;

interface EnrollmentRepositoryInterface
{
    public function save(Enrollment $enrollment): void;

    public function findById(EnrollmentId $id): ?Enrollment;

    /**
     * @param StudentId $studentId
     * @return Enrollment[]
     */
    public function findByStudentId(StudentId $studentId): array;

    public function existsByRegistrationNumber(RegistrationNumber $registrationNumber): bool;
}
