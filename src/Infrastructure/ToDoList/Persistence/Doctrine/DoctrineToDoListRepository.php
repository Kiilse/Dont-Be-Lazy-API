<?php

declare(strict_types=1);

namespace App\Infrastructure\ToDoList\Persistence\Doctrine;

use App\Domain\ToDoList\Model\ToDoList;
use App\Domain\ToDoList\Repository\ToDoListRepositoryInterface;
use App\Domain\ToDoList\ValueObject\ToDoListId;
use App\Domain\ToDoList\ValueObject\ToDoMode;
use App\Domain\ToDoList\ValueObject\ToDoTimerType;
use App\Domain\User\ValueObject\UserId;
use Doctrine\DBAL\Connection;

final readonly class DoctrineToDoListRepository implements ToDoListRepositoryInterface
{
    private const TABLE = 'to_do_lists';

    public function __construct(
        private Connection $connection
    ) {
    }

    public function save(ToDoList $toDoList): void
    {
        $data = [
            'to_do_id' => $toDoList->id()->value(),
            'user_id' => $toDoList->userId()->value(),
            'title' => $toDoList->title(),
            'mode' => $toDoList->mode()->value,
            'timer_type' => $toDoList->timerType()->value,
            'timer_value' => $toDoList->timerValue(),
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $exists = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::TABLE)
            ->where('to_do_id = :to_do_id')
            ->setParameter('to_do_id', $toDoList->id()->value())
            ->fetchOne() > 0;

        if ($exists) {
            $this->connection->update(self::TABLE, $data, ['to_do_id' => $toDoList->id()->value()]);
        } else {
            $data['created_at'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $this->connection->insert(self::TABLE, $data);
        }
    }

    public function findById(ToDoListId $toDoListId): ?ToDoList
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('to_do_id = :to_do_id')
            ->setParameter('to_do_id', $toDoListId->value())
            ->fetchAssociative();

        if (!$row) {
            return null;
        }

        return ToDoList::reconstitute(
            ToDoListId::fromString($row['to_do_id']),
            UserId::fromString($row['user_id']),
            $row['title'],
            ToDoMode::from($row['mode']),
            ToDoTimerType::from($row['timer_type']),
            $row['timer_value']
        );
    }

    public function delete(ToDoList $toDoList): void
    {
        $this->connection->delete(self::TABLE, ['to_do_id' => $toDoList->id()->value()]);
    }
}
