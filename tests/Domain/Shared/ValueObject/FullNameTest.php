<?php

declare(strict_types=1);

namespace App\Tests\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\FullName;
use PHPUnit\Framework\TestCase;

class FullNameTest extends TestCase
{
    public function testValidFullName(): void
    {
        $value = 'Daniel Borges';
        $fullName = new FullName($value);

        $this->assertSame($value, $fullName->getValue());
    }

    public function testInvalidFullNameEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome não pode ser vazio.');

        new FullName('');
    }

    public function testInvalidFullNameOnlyNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome completo não pode ter números.');

        new FullName('1122334455');
    }

    public function testInvalidFullNameWithLettersAndNumbers(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O nome completo não pode ter números.');

        new FullName('Daniel Borges1');
    }

    public function testInvalidFullNameLessFiveCharacters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('O nome deve ter pelo menos %d caracteres.', FullName::MIN_LENGTH)
        );

        new FullName("Dani");
    }

    public function testInvalidFullNameMoreThan255Characters(): void
    {
        $longFullName = str_repeat('a', FullName::MAX_LENGTH + 1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('O nome deve ter no máximo %d caracteres.', FullName::MAX_LENGTH)
        );

        new FullName($longFullName);
    }

    public function testInvalidFullNameFewerThanMinimumNameParts(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Digite o seu nome completo.');

        new FullName('Daniel');
    }
}
