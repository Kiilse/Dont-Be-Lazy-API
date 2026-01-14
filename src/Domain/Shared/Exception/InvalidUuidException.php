<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidUuidException extends DomainException
{
    public const CODE = 'INVALID_UUID';

    public static function withValue(string $value): self
    {
        return new self(
            \sprintf('Invalid UUID format: "%s"', $value),
            self::CODE,
            400
        );
    }
}
