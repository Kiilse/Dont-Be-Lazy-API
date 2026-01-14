<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Model\User;
use App\Domain\User\ValueObject\UserId;

/**
 * Repository Interface 
 *
 * Cette interface est dans le Domain car elle définit un BESOIN métier.
 * L'implémentation est dans Infrastructure (ADAPTER).
 */
interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(UserId $id): User;

    public function findByEmail(Email $email): ?User;

    public function findByName(string $name): ?User;

    public function delete(User $user): void;
}
