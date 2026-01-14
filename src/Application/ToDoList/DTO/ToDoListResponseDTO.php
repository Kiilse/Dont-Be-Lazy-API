<?php

declare(strict_types=1);

namespace App\Application\ToDoList\DTO;

final readonly class ToDoListResponseDTO implements \JsonSerializable
{
    public function __construct(
        public string $userId,
        public string $toDoListId,
        public string $title,
        public string $mode,
        public string $timerType,
        public int $timerValue
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'userId' => $this->userId,
            'toDoListId' => $this->toDoListId,
            'title' => $this->title,
            'mode' => $this->mode,
            'timerType' => $this->timerType,
            'timerValue' => $this->timerValue
        ];
    }
}
