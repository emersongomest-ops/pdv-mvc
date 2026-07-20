<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Exceptions;

use App\Domain\Shared\ErrorCode;
use DomainException;

final class AuthenticationDomainException extends DomainException
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly ErrorCode $errorCode,
        public readonly array $context = [],
    ) {
        parent::__construct($errorCode->message());
    }
}
