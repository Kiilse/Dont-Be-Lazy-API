<?php

declare(strict_types=1);

namespace App\Application\User\Command\CreateUser;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Exception\InvalidUserException;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Command Handler: Implémente un cas d'usage métier
 *
 * Règle: Le handler orchestre le domain mais ne contient PAS de logique métier.
 * La logique métier est dans le Domain.
 */
final readonly class CreateUserCommandHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

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

        $user = User::create(
            $userId,
            $email,
            $command->name,
            $this->passwordHasher->hashPassword(
                new \Symfony\Component\Security\Core\User\User($command->email, $command->password),
                $command->password
            ),
            $role
        );

        $this->userRepository->save($user);

        return $userId;
    }
}
