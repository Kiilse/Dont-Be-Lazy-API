<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class DomainException extends \DomainException
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly int $httpStatusCode = 500
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
