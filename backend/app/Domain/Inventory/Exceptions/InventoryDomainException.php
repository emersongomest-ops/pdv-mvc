<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Exceptions;

use App\Domain\Shared\ErrorCode;
use DomainException;

final class InventoryDomainException extends DomainException
{
    public function __construct(public readonly ErrorCode $errorCode)
    {
        parent::__construct($errorCode->message());
    }
}
