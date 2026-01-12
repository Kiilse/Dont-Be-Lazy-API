<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Security;

use App\Domain\User\Model\User as DomainUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Adapter User pour Symfony Security
 *
 * Adapte notre entité Domain User à l'interface Symfony UserInterface
 */
final readonly class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private DomainUser $domainUser
    ) {}

    public function getRoles(): array
    {
        return [$this->domainUser->role()->value];
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->domainUser->email()->value();
    }

    public function getPassword(): ?string
    {
        return $this->domainUser->password();
    }

    public function getDomainUser(): DomainUser
    {
        return $this->domainUser;
    }
}
