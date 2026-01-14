<?php

declare(strict_types=1);

namespace App\Domain\ToDoList\ValueObject;

enum ToDoTimerType: string
{
    case FIX = 'TIMER_FIX';
    case FLEX = 'TIMER_FLEX';
    case CHRONO = 'TIMER_CHRONO';

    public function isFlex(): bool
    {
        return $this === self::FLEX;
    }

    public function isChrono(): bool
    {
        return $this === self::CHRONO;
    }
}
