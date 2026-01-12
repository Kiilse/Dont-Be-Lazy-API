<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

/**
 * Response DTO: Représentation des données pour l'API
 *
 * Pourquoi un DTO séparé de l'entité ?
 * - Contrôle ce qui est exposé (pas de fuite de données internes)
 * - Format optimisé pour l'API (différent du modèle métier)
 * - Versioning: peut avoir plusieurs DTOs pour une entité
 */
final readonly class UserResponseDTO implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $role,
        public bool $isActive
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role,
            'isActive' => $this->isActive,
        ];
    }
}
