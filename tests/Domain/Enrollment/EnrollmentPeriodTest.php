<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enrollment;

use App\Domain\Enrollment\EnrollmentPeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class EnrollmentPeriodTest extends TestCase
{
    private function currentYearDate(string $monthDay): DateTimeImmutable
    {
        $currentYear = (new DateTimeImmutable())->format('Y');
        return new DateTimeImmutable("{$currentYear}-{$monthDay}");
    }

    public function test_should_throw_domain_exception_when_start_year_is_before_current_year(): void
    {
        $startAt = new DateTimeImmutable('-1 year');
        $expiresAt = $startAt->modify('+1 month');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('A data de início da inscrição não pode ser anterior ao ano atual.');

        new EnrollmentPeriod($startAt, $expiresAt);
    }

    public function test_should_throw_domain_exception_when_start_year_is_more_than_one_year_in_future(): void
    {
        $startAt = new DateTimeImmutable('+2 years');
        $expiresAt = $startAt->modify('+1 month');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('A data de início da inscrição não pode ser agendada com mais de 1 ano de antecedência.');

        new EnrollmentPeriod($startAt, $expiresAt);
    }

    public function test_should_throw_domain_exception_when_expiration_year_is_not_same_as_start_year(): void
    {
        $startAt = $this->currentYearDate('01-01 00:00:00');
        $expiresAt = $startAt->modify('+2 years');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('A data de expiração deve ser do mesmo ano letivo da data de início.');

        new EnrollmentPeriod($startAt, $expiresAt);
    }

    public function test_should_throw_domain_exception_when_expiration_date_is_less_than_start_date(): void
    {
        $startAt = $this->currentYearDate('06-15 00:00:00');
        $expiresAt = $startAt->modify('-1 day');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('A data de expiração não pode ser menor que a data de início.');

        new EnrollmentPeriod($startAt, $expiresAt);
    }

    public function test_is_date_within_period(): void
    {
        $startAt = $this->currentYearDate('01-15 00:00:00');
        $expiresAt = $this->currentYearDate('06-15 00:00:00');
        $dateWithinPeriod = $this->currentYearDate('03-01 00:00:00');

        $enrollmentPeriod = new EnrollmentPeriod($startAt, $expiresAt);

        $this->assertTrue($enrollmentPeriod->isDateWithPeriod($dateWithinPeriod));
    }

    public function test_two_enrollment_periods_with_same_dates_are_equal(): void
    {
        $startAt = $this->currentYearDate('01-15 00:00:00');
        $expiresAt = $this->currentYearDate('06-15 00:00:00');

        $periodOne = new EnrollmentPeriod($startAt, $expiresAt);
        $periodTwo = new EnrollmentPeriod($startAt, $expiresAt);

        $this->assertTrue($periodOne->equals($periodTwo));
    }
}
