<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;


final class ToDoListNotFoundException extends DomainException
{
    public const CODE = 'TO_DO_LIST_NOT_FOUND';
}
