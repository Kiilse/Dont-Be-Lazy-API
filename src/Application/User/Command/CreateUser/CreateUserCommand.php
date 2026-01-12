<?php

declare(strict_types=1);

namespace App\Application\User\Command\CreateUser;

/**
 * Command: DTO immuable qui décrit une intention
 *
 * Règle: Les Commands ne retournent rien ou un ID.
 * Elles modifient l'état du système.
 */
final readonly class CreateUserCommand
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public string $role = 'ROLE_USER'
    ) {}
}
