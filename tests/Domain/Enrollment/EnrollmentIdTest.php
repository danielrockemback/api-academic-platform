<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enrollment;

use App\Domain\Enrollment\EnrollmentId;
use PHPUnit\Framework\TestCase;

class EnrollmentIdTest extends TestCase
{
    public function test_create_enrollment_id_valid(): void
    {
        $value = 10;
        $enrollmentId = new EnrollmentId($value);

        $this->assertSame($value, $enrollmentId->getValue());
    }

    public function test_create_enrollment_id_invalid(): void
    {
        $id = -10;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O ID da inscrição dever ser um número positivo.');

        new EnrollmentId($id);
    }

    public function test_create_enrollment_id_equals(): void
    {
        $value = 11;
        $valueTwo = 11;

        $enrollmentId = new EnrollmentId($value);
        $enrollmentIdTwo = new EnrollmentId($valueTwo);

        $this->assertTrue($enrollmentId->equals($enrollmentIdTwo));
    }

    public function test_create_enrollment_id_not_equals(): void
    {
        $value = 11;
        $valueTwo = 22;

        $enrollmentId = new EnrollmentId($value);
        $enrollmentIdTwo = new EnrollmentId($valueTwo);

        $this->assertFalse($enrollmentId->equals($enrollmentIdTwo));
    }
}
