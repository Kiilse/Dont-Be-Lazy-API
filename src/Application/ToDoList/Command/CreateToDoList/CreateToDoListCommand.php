<?php

declare(strict_types=1);

namespace App\Application\ToDoList\Command\CreateToDoList;

use App\Domain\ToDoList\ValueObject\ToDoMode;
use App\Domain\ToDoList\ValueObject\ToDoTimerType;
use App\Domain\User\ValueObject\UserId;

final readonly class CreateToDoListCommand
{
    public function __construct(
        public UserId $userId,
        public string $title,
        public ToDoMode $mode = ToDoMode::CLASSIQUE,
        public ToDoTimerType $timerType = ToDoTimerType::FIX,
        public int $timerValue = 30
    ) {}
}
