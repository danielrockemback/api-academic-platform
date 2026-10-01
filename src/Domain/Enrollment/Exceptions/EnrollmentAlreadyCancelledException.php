<?php

declare(strict_types=1);

namespace App\Domain\Enrollment\Exceptions;

final class EnrollmentAlreadyCancelledException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Não é possível cancelar essa matrícula, pois ela já está cancelada no nosso sistema.');
    }
}
