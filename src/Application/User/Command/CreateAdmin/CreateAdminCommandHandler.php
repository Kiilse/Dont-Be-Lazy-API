<?php

declare(strict_types=1);

namespace App\Application\User\Command\CreateAdmin;

use App\Application\User\Command\CreateUser\CreateUserCommand;
use App\Application\User\Command\CreateUser\CreateUserCommandHandler;

/**
 * Handler spécialisé pour créer un admin
 * Réutilise le handler CreateUser avec le rôle ADMIN
 */
final readonly class CreateAdminCommandHandler
{
    public function __construct(
        private CreateUserCommandHandler $createUserHandler
    ) {}

    public function __invoke(CreateAdminCommand $command): \App\Domain\User\ValueObject\UserId
    {
        $createUserCommand = new CreateUserCommand(
            email: $command->email,
            name: $command->name,
            password: $command->password,
            role: 'ROLE_ADMIN'
        );

        return ($this->createUserHandler)($createUserCommand);
    }
}
