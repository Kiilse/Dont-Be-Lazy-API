<?php

declare(strict_types=1);

namespace App\Application\ToDoList\Query\GetToDoList;

use App\Domain\ToDoList\Repository\ToDoListRepositoryInterface;
use App\Domain\ToDoList\ValueObject\ToDoListId;
use App\Application\ToDoList\DTO\ToDoListResponseDTO;
use App\Application\ToDoList\Query\GetToDoList\GetToDoListQuery;

final readonly class GetToDoListQueryHandler
{
    public function __construct(
        private ToDoListRepositoryInterface $toDoListRepository
    ) {}

    public function __invoke(GetToDoListQuery $query): ToDoListResponseDTO
    {
        $toDoListId = ToDoListId::fromString($query->toDoListId);
        $toDoList = $this->toDoListRepository->findById($toDoListId);

        return new ToDoListResponseDTO(
            userId: $toDoList->userId()->value(),
            toDoListId: $toDoList->id()->value(),
            title: $toDoList->title(),
            mode: $toDoList->mode()->value,
            timerType: $toDoList->timerType()->value,
            timerValue: $toDoList->timerValue()
        );
    }
}
