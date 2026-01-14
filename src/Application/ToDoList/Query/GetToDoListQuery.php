<?php

declare(strict_types=1);

namespace App\Application\ToDoList\Query;

final readonly class GetToDoListQuery
{
    public function __construct(
        public string $toDoListId
    ) {}
}
