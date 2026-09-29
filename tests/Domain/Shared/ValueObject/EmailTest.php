<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_creates_valid_email(): void
    {
        $value = 'daniel@exemplo.com';
        $email = new Email($value);

        $this->assertSame($value, $email->getValue());
    }

    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Email('email-invalido');
    }

    public function test_two_emails_with_same_email_are_equal(): void
    {
        $value = 'daniel@exemplo.com';

        $email = new Email($value);
        $emailTwo = new Email($value);

        $this->assertTrue($email->equals($emailTwo));
    }

    public function test_two_emails_with_different_value_are_not_equal(): void
    {
        $email = new Email('daniel@exemplo.com');
        $emailTwo = new Email('maria@exemplo.com');

        $this->assertFalse($email->equals($emailTwo));
    }
}
