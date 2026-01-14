<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Security;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException as SymfonyUserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $email = Email::fromString($identifier);
        $domainUser = $this->userRepository->findByEmail($email);

        if ($domainUser === null) {
            throw new SymfonyUserNotFoundException();
        }

        return new User($domainUser);
    }
}
