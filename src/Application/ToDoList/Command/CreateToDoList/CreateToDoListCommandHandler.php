<?php

declare(strict_types=1);

namespace App\Application\ToDoList\Command\CreateToDoList;

use App\Domain\ToDoList\Repository\ToDoListRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\Shared\Exception\UserNotFoundException;
use App\Domain\ToDoList\Model\ToDoList;
use App\Domain\ToDoList\ValueObject\ToDoListId;

final readonly class CreateToDoListCommandHandler
{
    public function __construct(
        private ToDoListRepositoryInterface $toDoListRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(CreateToDoListCommand $command): TodoListId
    {
        if (!$this->userRepository->findById($command->userId)) {
            throw UserNotFoundException::withId($command->userId);
        }

        $toDoList = ToDoList::create(
            $command->userId,
            $command->title,
            $command->mode,
            $command->timerType,
            $command->timerValue
        );

        $this->toDoListRepository->save($toDoList);

        return $toDoList->id();
    }
}
