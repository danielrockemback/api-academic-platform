<?php

declare(strict_types=1);

namespace App\Tests\Domain\Student;

use App\Domain\Student\StudentId;
use PHPUnit\Framework\TestCase;

class StudentIdTest extends TestCase
{
    public function testCreateStudentIdValid(): void
    {
        $value = 1;
        $id = new StudentId($value);

        $this->assertSame($value, $id->getValue());
    }

    public function testCreateStudentIdWithNegativeValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new StudentId(-1);
    }

    public function testCreateStudentIdWithStringThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        new StudentId('abc');
    }

    public function testCreateTwoStudentsWithTheSameId(): void
    {
        $value = 10;
        $valueTwo = 10;

        $studentId = new StudentId($value);
        $studentIdTwo = new StudentId($valueTwo);

        $this->assertTrue($studentId->equals($studentIdTwo));
    }

    public function testCreateTwoStudentsWithDifferentId(): void
    {
        $value = 10;
        $valueTwo = 11;

        $studentId = new StudentId($value);
        $studentIdTwo = new StudentId($valueTwo);

        $this->assertFalse($studentId->equals($studentIdTwo));
    }
}
