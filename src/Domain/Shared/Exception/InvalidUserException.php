<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidUserException extends DomainException
{
    public const CODE = 'INVALID_USER';

    public static function emptyName(): self
    {
        return new self(
            'User name cannot be empty',
            self::CODE,
            400
        );
    }

    public static function duplicateEmail(): self
    {
        return new self(
            'Email already exists',
            self::CODE,
            409
        );
    }

    public static function duplicateName(): self
    {
        return new self(
            'Username already exists',
            self::CODE,
            409
        );
    }

    public static function alreadyDeactivated(): self
    {
        return new self(
            'User is already deactivated',
            self::CODE,
            400
        );
    }
}
