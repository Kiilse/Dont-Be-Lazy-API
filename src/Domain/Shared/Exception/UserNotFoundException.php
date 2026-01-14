<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use App\Domain\User\ValueObject\UserId;

final class UserNotFoundException extends DomainException
{
    public const CODE = 'USER_NOT_FOUND';

    public static function withId(UserId $id): self
    {
        return new self(
            \sprintf('User with ID "%s" not found', $id->value()),
            self::CODE,
            404
        );
    }

    public static function withEmail(string $email): self
    {
        return new self(
            \sprintf('User with email "%s" not found', $email),
            self::CODE,
            404
        );
    }
}
