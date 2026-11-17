<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\FullName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FullNameTest extends TestCase
{
    public function test_valid_full_name(): void
    {
        $value = 'Daniel Borges';
        $fullName = new FullName($value);

        $this->assertSame($value, $fullName->getValue());
    }

    public function test_invalid_full_name_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome não pode ser vazio.');

        new FullName('');
    }

    public function test_invalid_full_name_only_number(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome completo não pode ter números.');

        new FullName('1122334455');
    }

    public function test_invalid_full_name_with_letters_and_numbers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome completo não pode ter números.');

        new FullName('Daniel Borges1');
    }

    public function test_invalid_full_name_less_five_characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('O nome deve ter pelo menos %d caracteres.', FullName::MIN_LENGTH)
        );

        new FullName("Dani");
    }

    public function test_invalid_full_name_more_than_255_characters(): void
    {
        $longFullName = str_repeat('a', FullName::MAX_LENGTH + 1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('O nome deve ter no máximo %d caracteres.', FullName::MAX_LENGTH)
        );

        new FullName($longFullName);
    }

    public function test_invalid_full_name_fewer_than_minimum_name_parts(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Digite o seu nome completo.');

        new FullName('Daniel');
    }

    #[DataProvider('namesWithSurroundingWhitespaceProvider')]
    public function test_invalid_full_name_with_surrounding_whitespace(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome não pode começar ou terminar com espaços.');

        new FullName($value);
    }

    public static function namesWithSurroundingWhitespaceProvider(): array
    {
        return [
            'espaço no início' => [' Daniel Borges'],
            'espaço no fim' => ['Daniel Borges '],
            'espaços nas duas pontas' => [' Daniel Borges '],
            'tab no início' => ["\tDaniel Borges"],
            'quebra de linha no fim' => ["Daniel Borges\n"],
        ];
    }

    public function test_invalid_full_name_only_whitespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome não pode ser vazio.');

        new FullName('     ');
    }

    public function test_valid_full_name_with_double_space_in_the_middle(): void
    {
        $value = 'Daniel  Borges';
        $fullName = new FullName($value);

        $this->assertSame($value, $fullName->getValue());
    }

    #[DataProvider('validCompoundNamesProvider')]
    public function test_valid_full_name_with_hyphen_or_apostrophe(string $value): void
    {
        $fullName = new FullName($value);

        $this->assertSame($value, $fullName->getValue());
    }

    public static function validCompoundNamesProvider(): array
    {
        return [
            'hífen' => ['Ana-Maria Souza'],
            'apóstrofo' => ["João D'Ávila"],
        ];
    }

    #[DataProvider('namesWithInvalidCharactersProvider')]
    public function test_invalid_full_name_with_invalid_characters(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome contém caracteres inválidos.');

        new FullName($value);
    }

    public static function namesWithInvalidCharactersProvider(): array
    {
        return [
            'símbolo' => ['Daniel @Borges'],
            'ponto' => ['Daniel B. Borges'],
            'hífen solto' => ['Daniel - Borges'],
            'hífen duplo' => ['Ana--Maria Souza'],
            'apóstrofo no fim de palavra' => ["Daniel' Borges"],
            'tab no meio' => ["Daniel\tBorges"],
        ];
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $fullName = new FullName('Daniel Borges');

        $this->assertTrue($fullName->equals(new FullName('Daniel Borges')));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $fullName = new FullName('Daniel Borges');

        $this->assertFalse($fullName->equals(new FullName('Daniel Souza')));
    }
}
