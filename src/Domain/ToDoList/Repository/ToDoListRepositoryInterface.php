<?php

declare(strict_types=1);

namespace App\Domain\ToDoList\Repository;

use App\Domain\ToDoList\Model\ToDoList;
use App\Domain\ToDoList\ValueObject\ToDoListId;

interface ToDoListRepositoryInterface
{
    public function create(): bool;

    public function save(ToDoList $toDoList): void;

    public function findById(ToDoListId $toDoListId): ?ToDoList;
}
