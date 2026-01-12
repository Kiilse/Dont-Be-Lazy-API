<?php

declare(strict_types=1);

namespace App\Application\User\Query\GetUser;

/**
 * Query: DTO immuable qui décrit une requête de lecture
 *
 * Règle: Les Queries ne modifient JAMAIS l'état.
 * Elles retournent des DTOs, jamais des entités.
 */
final readonly class GetUserQuery
{
    public function __construct(
        public string $userId
    ) {}
}
