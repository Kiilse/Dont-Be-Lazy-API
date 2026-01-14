<?php

declare(strict_types=1);

namespace App\Application\User\Command\CreateUser;

use App\Domain\Shared\Exception\InvalidUserException;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final readonly class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(CreateUserCommand $command): UserId
    {
        $email = Email::fromString($command->email);

        if ($this->userRepository->findByEmail($email) !== null) {
            throw InvalidUserException::duplicateEmail();
        }

        if ($this->userRepository->findByName($command->name) !== null) {
            throw InvalidUserException::duplicateName();
        }

        $role = UserRole::from($command->role);
        $userId = UserId::generate();

        $tempUser = new class($command->email) implements PasswordAuthenticatedUserInterface {
            public function __construct(private string $email)
            {
            }

            public function getPassword(): ?string
            {
                return null;
            }

            public function getUserIdentifier(): string
            {
                return $this->email;
            }
        };

        $user = User::create(
            $userId,
            $email,
            $command->name,
            $this->passwordHasher->hashPassword($tempUser, $command->password),
            $role
        );

        $this->userRepository->save($user);

        return $userId;
    }
}
