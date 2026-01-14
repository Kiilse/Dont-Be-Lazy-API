<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

final readonly class UserResponseDTO implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public string $role,
        public bool $isActive
    ) {
    }

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
