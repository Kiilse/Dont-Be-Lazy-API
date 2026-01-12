<?php

declare(strict_types=1);

namespace App\Application\User\Query\GetUser;

use App\Application\User\DTO\UserResponseDTO;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;

/**
 * Query Handler: Implémente une requête de lecture
 *
 * Règle: Ne modifie jamais l'état, retourne uniquement des DTOs.
 */
final readonly class GetUserQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(GetUserQuery $query): UserResponseDTO
    {
        $userId = UserId::fromString($query->userId);
        $user = $this->userRepository->findById($userId);

        return new UserResponseDTO(
            id: $user->id()->value(),
            email: $user->email()->value(),
            name: $user->name(),
            role: $user->role()->value,
            isActive: $user->isActive()
        );
    }
}
