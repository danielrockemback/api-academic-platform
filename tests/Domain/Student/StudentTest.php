<?php

declare(strict_types=1);

namespace App\Tests\Domain\Student;

use App\Domain\Shared\ValueObject\BirthDate;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\FullName;
use App\Domain\Student\Student;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StudentTest extends TestCase
{
    private function yearsAgo(int $years, string $adjust = ''): string
    {
        return (new DateTimeImmutable('today'))
            ->modify("-{$years} years {$adjust}")
            ->format('Y-m-d');
    }

    private function createStudent(string $birthDate): Student
    {
        return new Student(
            null,
            new FullName('Daniel Borges'),
            new Email('daniel@exemplo.com'),
            new BirthDate($birthDate),
        );
    }

    public function test_create_student_with_exactly_minimum_age(): void
    {
        $student = $this->createStudent($this->yearsAgo(16));

        $this->assertInstanceOf(Student::class, $student);
    }

    public function test_should_throw_exception_when_student_is_one_day_younger_than_minimum_age(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Não são permitidos alunos com menos de 16 anos.');

        $this->createStudent($this->yearsAgo(16, '+1 day'));
    }

    public function test_is_minor_returns_true_when_student_is_17(): void
    {
        $this->assertTrue($this->createStudent($this->yearsAgo(17))->isMinor());
    }

    public function test_is_minor_returns_true_one_day_before_turning_18(): void
    {
        $this->assertTrue($this->createStudent($this->yearsAgo(18, '+1 day'))->isMinor());
    }

    public function test_is_minor_returns_false_when_student_is_exactly_18(): void
    {
        $this->assertFalse($this->createStudent($this->yearsAgo(18))->isMinor());
    }

    public function test_getters_return_the_given_values(): void
    {
        $fullName = new FullName('Daniel Borges');
        $email = new Email('daniel@exemplo.com');
        $birthDate = new BirthDate($this->yearsAgo(20));

        $student = new Student(null, $fullName, $email, $birthDate);

        $this->assertNull($student->getId());
        $this->assertSame($fullName, $student->getFullName());
        $this->assertSame($email, $student->getEmail());
        $this->assertSame($birthDate, $student->getBirthDate());
    }
}
