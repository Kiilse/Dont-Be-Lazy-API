<?php

declare(strict_types=1);

namespace App\Domain\ToDoList\ValueObject;

enum ToDoMode: string
{
    case CHALLENGE = 'MODE_CHALLENGE';
    case CLASSIQUE = 'MODE_CLASSIQUE';

    public function isChallenge(): bool
    {
        return $this === self::CHALLENGE;
    }
}
