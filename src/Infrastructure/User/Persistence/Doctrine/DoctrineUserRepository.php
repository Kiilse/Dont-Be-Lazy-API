<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Persistence\Doctrine;

use App\Domain\Shared\Exception\UserNotFoundException;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserRole;
use Doctrine\DBAL\Connection;

final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    private const TABLE = 'users';

    public function __construct(
        private Connection $connection
    ) {
    }

    public function save(User $user): void
    {
        $data = [
            'id' => $user->id()->value(),
            'email' => $user->email()->value(),
            'name' => $user->name(),
            'password' => $user->password(),
            'role' => $user->role()->value,
            'is_active' => $user->isActive(),
            'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $exists = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $user->id()->value())
            ->fetchOne() > 0;

        if ($exists) {
            $this->connection->update(self::TABLE, $data, ['id' => $user->id()->value()]);
        } else {
            $data['created_at'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $this->connection->insert(self::TABLE, $data);
        }
    }

    public function findById(UserId $id): User
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $id->value())
            ->fetchAssociative();

        if (!$row) {
            throw UserNotFoundException::withId($id);
        }

        return User::reconstitute(
            UserId::fromString($row['id']),
            Email::fromString($row['email']),
            $row['name'],
            $row['password'],
            UserRole::from($row['role']),
            (bool) $row['is_active']
        );
    }

    public function findByEmail(Email $email): ?User
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('email = :email')
            ->setParameter('email', $email->value())
            ->fetchAssociative();

        if (!$row) {
            return null;
        }

        return User::reconstitute(
            UserId::fromString($row['id']),
            Email::fromString($row['email']),
            $row['name'],
            $row['password'],
            UserRole::from($row['role']),
            (bool) $row['is_active']
        );
    }

    public function findByName(string $name): ?User
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('name = :name')
            ->setParameter('name', $name)
            ->fetchAssociative();

        if (!$row) {
            return null;
        }

        return User::reconstitute(
            UserId::fromString($row['id']),
            Email::fromString($row['email']),
            $row['name'],
            $row['password'],
            UserRole::from($row['role']),
            (bool) $row['is_active']
        );
    }

    public function delete(User $user): void
    {
        $this->connection->delete(self::TABLE, ['id' => $user->id()->value()]);
    }
}
