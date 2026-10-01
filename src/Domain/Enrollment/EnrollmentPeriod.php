<?php

declare(strict_types=1);

namespace App\Domain\Enrollment;

final class EnrollmentPeriod
{
    public function __construct(
        private readonly \DateTimeImmutable $startAt,
        private readonly \DateTimeImmutable $expiresAt
    ) {
        $this->validate($startAt, $expiresAt);
    }

    private function validate(\DateTimeImmutable $startAt, \DateTimeImmutable $expiresAt)
    {
        $enrollmentStartYear = (int) $startAt->format('Y');
        $expirationYear = (int) $expiresAt->format('Y');
        $currentYear = (int) (new \DateTimeImmutable())->format('Y');

        if ($enrollmentStartYear < $currentYear) {
            throw new \DomainException('A data de início da inscrição não pode ser anterior ao ano atual.');
        }

        if (($enrollmentStartYear - $currentYear) > 1) {
            throw new \DomainException('A data de início da inscrição não pode ser agendada com mais de 1 ano de antecedência.');
        }

        if ($expiresAt < $startAt) {
            throw new \DomainException('A data de expiração não pode ser menor que a data de início.');
        }

        if ($expirationYear !== $enrollmentStartYear) {
            throw new \DomainException('A data de expiração deve ser do mesmo ano letivo da data de início.');
        }
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isDateWithPeriod(\DateTimeImmutable $date): bool
    {
        return $date >= $this->getStartAt() && $date <= $this->getExpiresAt();
    }

    public function equals(EnrollmentPeriod $other): bool
    {
        return $this->getStartAt() == $other->getStartAt() && $this->getExpiresAt() == $other->getExpiresAt();
    }
}
