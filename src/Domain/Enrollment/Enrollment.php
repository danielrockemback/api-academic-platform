<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

use App\Domain\Enrollment\Exceptions\EnrollmentAlreadyCancelledException;
use App\Domain\Enrollment\Exceptions\InvalidEnrollmentDateException;
use App\Domain\Student\StudentId;

final class Enrollment
{
    private bool $isActive = true;

    public function __construct(
        private readonly StudentId $studentId,
        private readonly RegistrationNumber $registrationNumber,
        private readonly EnrollmentPeriod $period,
        private ?\DateTimeImmutable $cancelledAt = null,
        private readonly ?EnrollmentId $id = null,
    ) {
    }

    public function cancel(\DateTimeImmutable $date): void
    {
        if (!$this->isActive) {
            throw new EnrollmentAlreadyCancelledException();
        }

        if (!$this->period->isDateWithPeriod($date)) {
            throw new InvalidEnrollmentDateException(
                'A data de cancelamento deve estar entre da data de matrícula e a data final do ano letivo.'
            );
        }

        $this->isActive = false;
        $this->cancelledAt = $date;
    }

    public function getId(): ?EnrollmentId
    {
        return $this->id;
    }

    public function getStudentId(): StudentId
    {
        return $this->studentId;
    }

    public function getRegistrationNumber(): RegistrationNumber
    {
        return $this->registrationNumber;
    }

    public function getPeriod(): EnrollmentPeriod
    {
        return $this->period;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
