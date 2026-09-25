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

    public function testCreatesValidRegistrationNumber(): void
    {
        $value = "{$this->currentYear}-00001";

        $registrationNumber = new RegistrationNumber($value);

        $this->assertSame($value, $registrationNumber->getValue());
    }

    public function testThrowsExceptionWhenValueIsEmpty(): void
    {
        $value = '';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O número de registro não pode ser vazio');

        new RegistrationNumber($value);
    }

    public function testThrowsExceptionWhenValueHasNoHyphen(): void
    {
        $formatRegistrationNumber = self::FORMART_REGISTRATION_NUMBER;
        $value = "{$this->currentYear}00001";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O número de registro deve conter um hífen. {$formatRegistrationNumber}");

        new RegistrationNumber($value);
    }

    public function testThrowsExceptionWhenLengthIsNotTenCharacters(): void
    {
        $formatRegistrationNumber = self::FORMART_REGISTRATION_NUMBER;
        $value = "{$this->currentYear}-1234";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O número de registro tem que ter 10 caracteres. {$formatRegistrationNumber}");

        new RegistrationNumber($value);
    }

    public function testThrowsExceptionWhenYearPartIsNotNumeric(): void
    {
        $value = '202K-12345';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Os 4 primeiros caracteres devem ser números.');

        new RegistrationNumber($value);
    }

    public function testThrowsExceptionWhenYearIsNotCurrentYear(): void
    {
        $lastYear = (new DateTimeImmutable('-1 year'))->format('Y');
        $value = $lastYear . '-12345';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Os 4 primeiros caracteres devem ser representado pelo ano vigente.');

        new RegistrationNumber($value);
    }

    public function testThrowsExceptionWhenSequentialNumberIsZeroOrLess(): void
    {
        $value = "{$this->currentYear}-00000";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O número sequencial caracteres deve ser maior que 0.');

        new RegistrationNumber($value);
    }

    public function testTwoRegistrationNumbersWithSameValueAreEqual(): void
    {
        $valueOne = "{$this->currentYear}-00001";
        $valueTwo = "{$this->currentYear}-00001";

        $registrationNumberOne = new RegistrationNumber($valueOne);
        $registrationNumberTwo = new RegistrationNumber($valueTwo);

        $this->assertTrue($registrationNumberOne->equals($registrationNumberTwo));
    }

    public function testTwoRegistrationNumbersWithDifferentValueAreNotEqual(): void
    {
        $valueOne = "{$this->currentYear}-00001";
        $valueTwo = "{$this->currentYear}-00009";

        $registrationNumberOne = new RegistrationNumber($valueOne);
        $registrationNumberTwo = new RegistrationNumber($valueTwo);

        $this->assertFalse($registrationNumberOne->equals($registrationNumberTwo));
    }
}
