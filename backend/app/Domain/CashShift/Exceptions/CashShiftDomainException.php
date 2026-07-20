<?php

declare(strict_types=1);

namespace App\Domain\CashShift\Exceptions;

use App\Domain\Shared\ErrorCode;
use DomainException;

final class CashShiftDomainException extends DomainException
{
    public function __construct(public readonly ErrorCode $errorCode)
    {
        parent::__construct($errorCode->message());
    }
}
