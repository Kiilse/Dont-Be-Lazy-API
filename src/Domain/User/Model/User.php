<?php

declare(strict_types=1);

namespace App\Domain\User\Model;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\Exception\InvalidUserException;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserRole;

/**
 * User Domain Entity
 *
 * Contient UNIQUEMENT la logique métier.
 * Ne connaît rien de la base de données, du framework, ou de HTTP.
 */
final class User
{
    private function __construct(
        private readonly UserId $id,
        private Email $email,
        private string $name,
        private string $password,
        private UserRole $role,
        private bool $isActive
    ) {}

    public static function create(
        UserId $id,
        Email $email,
        string $name,
        string $hashedPassword,
        UserRole $role = UserRole::USER
    ): self {
        if (empty(trim($name))) {
            throw InvalidUserException::emptyName();
        }

        return new self($id, $email, trim($name), $hashedPassword, $role, true);
    }

    public static function reconstitute(
        UserId $id,
        Email $email,
        string $name,
        string $password,
        UserRole $role,
        bool $isActive
    ): self {
        return new self($id, $email, $name, $password, $role, $isActive);
    }

    public function deactivate(): void
    {
        if (!$this->isActive) {
            throw InvalidUserException::alreadyDeactivated();
        }

        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function changeName(string $name): void
    {
        if (empty(trim($name))) {
            throw InvalidUserException::emptyName();
        }

        $this->name = trim($name);
    }

    public function changeRole(UserRole $role): void
    {
        $this->role = $role;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function role(): UserRole
    {
        return $this->role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
