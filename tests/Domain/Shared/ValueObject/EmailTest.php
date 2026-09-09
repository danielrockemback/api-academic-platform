<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testCreatesValidEmail(): void
    {
        $value = 'daniel@exemplo.com';
        $email = new Email($value);

        $this->assertSame($value, $email->getValue());
    }

    public function testThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email('email-invalido');
    }

    public function testTwoEmailsWithSameEmailAreEqual(): void
    {
        $value = 'daniel@exemplo.com';

        $email = new Email($value);
        $emailTwo = new Email($value);

        $this->assertTrue($email->equals($emailTwo));
    }

    public function testTwoEmailsWithDifferentValueAreNotEqual(): void
    {
        $email = new Email('daniel@exemplo.com');
        $emailTwo = new Email('maria@exemplo.com');

        $this->assertFalse($email->equals($emailTwo));
    }
}
