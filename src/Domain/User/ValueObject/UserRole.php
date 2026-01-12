<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USER = 'ROLE_USER';
    case DEMO = 'ROLE_DEMO';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isDemo(): bool
    {
        return $this === self::DEMO;
    }
}
