<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enrollment;

use App\Domain\Enrollment\RegistrationNumber;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RegistrationNumberTest extends TestCase
{
    private const FORMART_REGISTRATION_NUMBER = 'AAAA-NNNNN';

    private string $currentYear;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currentYear = (new DateTimeImmutable())->format('Y');
    }

    public function test_creates_valid_registration_number(): void
    {
        $value = "{$this->currentYear}-00001";

        $registrationNumber = new RegistrationNumber($value);

        $this->assertSame($value, $registrationNumber->getValue());
    }

    public function test_throws_exception_when_value_is_empty(): void
    {
        $value = '';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O número de registro não pode ser vazio');

        new RegistrationNumber($value);
    }

    public function test_throws_exception_when_value_has_no_hyphen(): void
    {
        $formatRegistrationNumber = self::FORMART_REGISTRATION_NUMBER;
        $value = "{$this->currentYear}00001";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O número de registro deve conter um hífen. {$formatRegistrationNumber}");

        new RegistrationNumber($value);
    }

    public function test_throws_exception_when_length_is_not_ten_characters(): void
    {
        $formatRegistrationNumber = self::FORMART_REGISTRATION_NUMBER;
        $value = "{$this->currentYear}-1234";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O número de registro tem que ter 10 caracteres. {$formatRegistrationNumber}");

        new RegistrationNumber($value);
    }

    public function test_throws_exception_when_year_part_is_not_numeric(): void
    {
        $value = '202K-12345';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Os 4 primeiros caracteres devem ser números.');

        new RegistrationNumber($value);
    }

    public function test_throws_exception_when_year_is_not_current_year(): void
    {
        $lastYear = (new DateTimeImmutable('-1 year'))->format('Y');
        $value = $lastYear . '-12345';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Os 4 primeiros caracteres devem ser representado pelo ano vigente.');

        new RegistrationNumber($value);
    }

    public function test_throws_exception_when_sequential_number_is_zero_or_less(): void
    {
        $value = "{$this->currentYear}-00000";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O número sequencial caracteres deve ser maior que 0.');

        new RegistrationNumber($value);
    }

    public function test_two_registration_numbers_with_same_value_are_equal(): void
    {
        $valueOne = "{$this->currentYear}-00001";
        $valueTwo = "{$this->currentYear}-00001";

        $registrationNumberOne = new RegistrationNumber($valueOne);
        $registrationNumberTwo = new RegistrationNumber($valueTwo);

        $this->assertTrue($registrationNumberOne->equals($registrationNumberTwo));
    }

    public function test_two_registration_numbers_with_different_value_are_not_equal(): void
    {
        $valueOne = "{$this->currentYear}-00001";
        $valueTwo = "{$this->currentYear}-00009";

        $registrationNumberOne = new RegistrationNumber($valueOne);
        $registrationNumberTwo = new RegistrationNumber($valueTwo);

        $this->assertFalse($registrationNumberOne->equals($registrationNumberTwo));
    }
}
