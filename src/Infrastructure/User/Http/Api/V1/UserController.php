<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Http\Api\V1;

use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Query\GetUser\GetUserQuery;
use App\Application\User\Query\GetUser\GetUserQueryHandler;
use App\Domain\Shared\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/users', name: 'api_v1_users_')]
final readonly class UserController
{
    public function __construct(
        private GetUserQueryHandler $getUserHandler,
        private ValidatorInterface $validator
    ) {}

    /**
     * GET /api/v1/users/{id}
     * Get user by id
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        if (empty(trim($id))) {
            return new JsonResponse(
                ['error' => 'User ID is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $query = new GetUserQuery(userId: $id);
            $user = ($this->getUserHandler)($query);

            return new JsonResponse($user->jsonSerialize());
        } catch (DomainException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                ],
                $e->getHttpStatusCode()
            );
        }
    }
}
