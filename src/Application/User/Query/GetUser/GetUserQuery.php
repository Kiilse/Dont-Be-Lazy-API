<?php

declare(strict_types=1);

namespace App\Application\User\Query\GetUser;

final readonly class GetUserQuery
{
    public function __construct(
        public string $userId
    ) {}
}
