<?php

declare(strict_types=1);

namespace App\Domain\ToDoList\Model;

use App\Domain\ToDoList\ValueObject\ToDoListId;
use App\Domain\ToDoList\ValueObject\ToDoMode;
use App\Domain\ToDoList\ValueObject\ToDoTimerType;
use App\Domain\User\ValueObject\UserId;

final class ToDoList
{
    private function __construct(
        private readonly UserId $user,
        private readonly ToDoListId $id,
        private string $title,
        private readonly ToDoMode $mode,
        private readonly ToDoTimerType $timerType,
        private readonly int $timerValue
    ) {}

    public static function create(
        UserId $userId,
        string $title,
        ToDoMode $mode,
        ToDoTimerType $timerType,
        int $timerValue
    ): self {
        $id = ToDoListId::generate();

        return new self($userId, $id, $title, $mode, $timerType, $timerValue);
    }

    public function id(): ToDoListId
    {
        return $this->id;
    }
}
