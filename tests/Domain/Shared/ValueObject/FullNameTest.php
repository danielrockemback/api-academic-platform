<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\FullName;
use PHPUnit\Framework\TestCase;

class FullNameTest extends TestCase
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
}
