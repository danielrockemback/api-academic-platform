<?php

declare(strict_types=1);

namespace App\Domain\Enrollment\Exceptions;

final class EnrollmentAlreadyCancelledException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Esta matrícula já está cancelada.');
    }
}
