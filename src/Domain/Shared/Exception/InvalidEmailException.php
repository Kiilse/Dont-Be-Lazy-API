<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidEmailException extends DomainException
{
    public const CODE = 'INVALID_EMAIL';

    public static function withValue(string $value): self
    {
        return new self(
            sprintf('Invalid email format: "%s"', $value),
            self::CODE,
            400
        );
    }
}
