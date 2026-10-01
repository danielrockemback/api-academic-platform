<?php

declare(strict_types=1);

namespace App\Tests\Domain\Student;

use App\Domain\Student\StudentId;
use PHPUnit\Framework\TestCase;

class StudentIdTest extends TestCase
{
    public function test_create_student_id_valid(): void
    {
        $value = 1;
        $id = new StudentId($value);

        $this->assertSame($value, $id->getValue());
    }

    public function test_create_student_id_with_negative_value_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new StudentId(-1);
    }

    public function test_create_student_id_with_string_throws_type_error(): void
    {
        $this->expectException(\TypeError::class);

        new StudentId('abc');
    }

    public function test_create_two_students_with_the_same_id(): void
    {
        $value = 10;
        $valueTwo = 10;

        $studentId = new StudentId($value);
        $studentIdTwo = new StudentId($valueTwo);

        $this->assertTrue($studentId->equals($studentIdTwo));
    }

    public function test_create_two_students_with_different_id(): void
    {
        $value = 10;
        $valueTwo = 11;

        $studentId = new StudentId($value);
        $studentIdTwo = new StudentId($valueTwo);

        $this->assertFalse($studentId->equals($studentIdTwo));
    }
}
