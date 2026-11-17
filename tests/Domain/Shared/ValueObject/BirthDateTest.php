<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\BirthDate;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BirthDateTest extends TestCase
{
    private function yearsAgo(int $years, string $adjust = ''): string
    {
        return (new DateTimeImmutable('today'))
            ->modify("-{$years} years {$adjust}")
            ->format('Y-m-d');
    }

    private function relativeDate(string $modifier): string
    {
        return (new DateTimeImmutable('today'))->modify($modifier)->format('Y-m-d');
    }

    public static function invalidFormatProvider(): array
    {
        return [
            'mes sem zero' => ['2000-1-15'],
            'espaco no inicio' => [' 2000-01-15'],
            'espaco no fim' => ['2000-01-15 '],
            'data inexistente' => ['2000-02-30'],
            'formato brasileiro' => ['15/01/2000'],
            'texto' => ['abc'],
        ];
    }

    public function test_create_birth_date_valid(): void
    {
        $value = $this->yearsAgo(20);

        $birthDate = new BirthDate($value);

        $this->assertSame($value, $birthDate->getValue());
    }

    public function test_should_throw_exception_when_value_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A data de nascimento deve ser preenchida.');

        new BirthDate('');
    }

    #[DataProvider('invalidFormatProvider')]
    public function test_should_throw_exception_when_format_is_invalid(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A data de nascimento está em um formato inválido.');

        new BirthDate($value);
    }

    public function test_should_throw_exception_when_date_is_tomorrow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Não é permitido informar uma data de nascimento no futuro.');

        new BirthDate($this->relativeDate('+1 day'));
    }

    public function test_should_throw_exception_when_date_is_one_year_in_the_future(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Não é permitido informar uma data de nascimento no futuro.');

        new BirthDate($this->relativeDate('+1 year'));
    }

    public function test_accepts_date_exactly_120_years_ago(): void
    {
        $value = $this->yearsAgo(120);

        $birthDate = new BirthDate($value);

        $this->assertSame($value, $birthDate->getValue());
    }

    public function test_should_throw_exception_when_date_is_older_than_120_years(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A data de nascimento não pode ultrapassar 120 anos.');

        new BirthDate($this->yearsAgo(120, '-1 day'));
    }

    public function test_age_when_birthday_is_today(): void
    {
        $birthDate = new BirthDate($this->yearsAgo(20));

        $this->assertSame(20, $birthDate->age());
    }

    public function test_age_when_birthday_is_tomorrow(): void
    {
        $birthDate = new BirthDate($this->yearsAgo(20, '+1 day'));

        $this->assertSame(19, $birthDate->age());
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $value = $this->yearsAgo(20);

        $this->assertTrue((new BirthDate($value))->equals(new BirthDate($value)));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $first = new BirthDate($this->yearsAgo(20));
        $second = new BirthDate($this->yearsAgo(21));

        $this->assertFalse($first->equals($second));
    }

    public function test_to_string_returns_value(): void
    {
        $value = $this->yearsAgo(20);

        $this->assertSame($value, (string) new BirthDate($value));
    }
}
