<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidUuidException;

/**
 * UUID Value Object.
 *
 * Encapsule un UUID avec validation.
 * Avantage : impossible d'avoir un UUID invalide dans le système.
 */
final readonly class Uuid implements \Stringable
{
    private function __construct(
        private string $value
    ) {
    }

    public static function generate(): self
    {
        return new self(\Symfony\Component\Uid\Uuid::v4()->toRfc4122());
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (!\Symfony\Component\Uid\Uuid::isValid($value)) {
            throw InvalidUuidException::withValue($value);
        }

        return new self($value);
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
