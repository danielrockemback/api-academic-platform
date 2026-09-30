<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enrollment;

use App\Domain\Enrollment\Enrollment;
use App\Domain\Enrollment\EnrollmentPeriod;
use App\Domain\Enrollment\Exceptions\EnrollmentAlreadyCancelledException;
use App\Domain\Enrollment\Exceptions\InvalidEnrollmentDateException;
use App\Domain\Enrollment\RegistrationNumber;
use App\Domain\Student\StudentId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EnrollmentTest extends TestCase
{
    private function currentYearDate(): string
    {
        $currentYear = (new DateTimeImmutable())->format('Y');

        return (string) $currentYear;
    }

    private function createEnrollment(?DateTimeImmutable $startAt = null, ?DateTimeImmutable $expiresAt = null): Enrollment
    {
        $currentYear = $this->currentYearDate();

        $studentId = new StudentId(1);
        $registrationNumber = new RegistrationNumber("{$currentYear}-00001");

        $startAt ??= new DateTimeImmutable("{$currentYear}-01-01 00:00:00");
        $expiresAt ??= new DateTimeImmutable("{$currentYear}-12-20 00:00:00");

        $period = new EnrollmentPeriod($startAt, $expiresAt);

        return new Enrollment($studentId, $registrationNumber, $period);
    }

    public function test_create_enrollment_valid(): void
    {
        $currentYear = $this->currentYearDate();

        $studentId = new StudentId(1);
        $registrationNumber = new RegistrationNumber("{$currentYear}-00001");
        $startAt = new DateTimeImmutable("{$currentYear}-01-01 00:00:00");
        $expiresAt = new DateTimeImmutable("{$currentYear}-12-20 00:00:00");
        $period = new EnrollmentPeriod($startAt, $expiresAt);

        $enrollment = new Enrollment($studentId, $registrationNumber, $period);

        $this->assertSame($studentId, $enrollment->getStudentId());
        $this->assertSame($registrationNumber, $enrollment->getRegistrationNumber());
        $this->assertSame($period, $enrollment->getPeriod());
        $this->assertTrue($enrollment->isActive());
        $this->assertNull($enrollment->getCancelledAt());
        $this->assertNull($enrollment->getId());
    }

    public function test_cancellation_of_enrollment(): void
    {
        $currentYear = $this->currentYearDate();
        $enrollment = $this->createEnrollment();
        $cancelDate = new DateTimeImmutable("{$currentYear}-06-15");

        $enrollment->cancel($cancelDate);

        $this->assertFalse($enrollment->isActive());
        $this->assertEquals($cancelDate, $enrollment->getCancelledAt());
    }

    public function test_should_throw_enrollment_already_cancelled_exception(): void
    {
        $currentYear = $this->currentYearDate();
        $enrollment = $this->createEnrollment();
        $cancelDate = new DateTimeImmutable("{$currentYear}-06-15");

        $enrollment->cancel($cancelDate);

        $this->expectException(EnrollmentAlreadyCancelledException::class);
        $this->expectExceptionMessage('Não é possível cancelar essa matrícula, pois ela já está cancelada no nosso sistema.');

        $enrollment->cancel($cancelDate);
    }

    public function test_should_throw_invalid_enrollment_date_exception_when_date_is_after_expiry_date(): void
    {
        $currentYear = $this->currentYearDate();
        $enrollment = $this->createEnrollment();
        $cancelDate = new DateTimeImmutable("{$currentYear}-12-21");

        $this->expectException(InvalidEnrollmentDateException::class);
        $this->expectExceptionMessage('A data de cancelamento deve estar entre da data de matrícula e a data final do ano letivo.');

        $enrollment->cancel($cancelDate);
    }

    public function test_should_throw_invalid_enrollment_date_exception_when_date_is_before_start_date(): void
    {
        $currentYear = $this->currentYearDate();
        $startAt = new DateTimeImmutable("{$currentYear}-10-01 00:00:00");
        $expiresAt = new DateTimeImmutable("{$currentYear}-12-20 00:00:00");
        $enrollment = $this->createEnrollment($startAt, $expiresAt);

        $cancelDate = new DateTimeImmutable("{$currentYear}-01-05");

        $this->expectException(InvalidEnrollmentDateException::class);
        $this->expectExceptionMessage('A data de cancelamento deve estar entre da data de matrícula e a data final do ano letivo.');

        $enrollment->cancel($cancelDate);
    }
}
