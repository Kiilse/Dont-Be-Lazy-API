<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Http\Api\V1;

use App\Application\User\Command\CreateUser\CreateUserCommand;
use App\Application\User\Command\CreateUser\CreateUserCommandHandler;
use App\Domain\Shared\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/v1/register', name: 'api_v1_register_')]
final readonly class RegisterController
{
    public function __construct(
        private CreateUserCommandHandler $createUserHandler,
        private ValidatorInterface $validator
    ) {}

    /**
     * POST /api/v1/register
     * Inscription d'un nouvel utilisateur
     */
    #[Route('', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
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
        } catch (DomainException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                ],
                $e->getHttpStatusCode()
            );
        } catch (\Throwable $e) {
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
}
