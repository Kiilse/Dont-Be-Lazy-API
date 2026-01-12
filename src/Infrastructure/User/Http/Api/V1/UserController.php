<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Http\Api\V1;

use App\Application\User\Command\CreateUser\CreateUserCommand;
use App\Application\User\Command\CreateUser\CreateUserCommandHandler;
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

/**
 * API Controller: HTTP Adapter
 *
 * Règle: Le Controller est MINCE. Il contient uniquement :
 * 1. Validation de la requête HTTP
 * 2. Transformation Request → Command/Query
 * 3. Appel du handler
 * 4. Transformation Résultat → HTTP Response
 *
 * Toute la logique métier est dans Application/Domain.
 */
#[Route('/api/v1/users', name: 'api_v1_users_')]
final readonly class UserController
{
    public function __construct(
        private CreateUserCommandHandler $createUserHandler,
        private GetUserQueryHandler $getUserHandler,
        private ValidatorInterface $validator
    ) {}

    /**
     * POST /api/v1/users
     * Crée un nouvel utilisateur
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'name' => [new Assert\NotBlank(), new Assert\Length(min: 1, max: 255)],
            'password' => [new Assert\NotBlank(), new Assert\Length(min: 8)],
            'role' => new Assert\Optional([new Assert\Choice(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_DEMO'])]),
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(
                ['errors' => $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Dans UserController, méthode create(), remplacez le try/catch par :

        try {
            $command = new CreateUserCommand(
                email: $data['email'],
                name: $data['name'],
                password: $data['password'],
                role: $data['role'] ?? 'ROLE_USER'
            );

            $userId = ($this->createUserHandler)($command);

            return new JsonResponse(
                ['id' => $userId->value()],
                Response::HTTP_CREATED,
                ['Location' => "/api/v1/users/{$userId->value()}"]
            );
        } catch (\App\Domain\Shared\Exception\DomainException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                ],
                $e->getHttpStatusCode()
            );
        } catch (\Throwable $e) {
            // Capturer toutes les autres exceptions pour le debug
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * GET /api/v1/users/{id}
     * Récupère un utilisateur par son ID
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
