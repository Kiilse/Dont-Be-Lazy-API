<?php

declare(strict_types=1);

namespace App\Application\User\Command\CreateUser;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public string $role = 'ROLE_USER'
    ) {
    }
}
