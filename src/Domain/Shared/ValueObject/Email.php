<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidEmailException;

/**
 * Email Value Object
 *
 * Encapsule un email avec validation.
 * Avantage : impossible d'avoir un email invalide dans le système.
 */
final readonly class Email implements \Stringable
{
    private function __construct(
        private string $value
    ) {}

    public static function fromString(string $email): self
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::withValue($email);
        }

        return new self($email);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
