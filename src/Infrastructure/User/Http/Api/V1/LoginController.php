<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Http\Api\V1;

use App\Domain\User\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route('/api/login', name: 'api_login')]
final readonly class LoginController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('', name: '', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(
                ['error' => 'Email and password are required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $email = \App\Domain\Shared\ValueObject\Email::fromString($data['email']);
            $user = $this->userRepository->findByEmail($email);

            if (!$user || !$user->isActive()) {
                throw new AuthenticationException('Invalid credentials');
            }

            $securityUser = new \App\Infrastructure\User\Security\User($user);

            if (!$this->passwordHasher->isPasswordValid($securityUser, $data['password'])) {
                throw new AuthenticationException('Invalid credentials');
            }

            $token = $this->jwtManager->create($securityUser);

            return new JsonResponse([
                'token' => $token,
                'user' => [
                    'id' => $user->id()->value(),
                    'email' => $user->email()->value(),
                    'name' => $user->name(),
                    'role' => $user->role()->value,
                ],
            ]);
        } catch (AuthenticationException $e) {
            return new JsonResponse(
                ['error' => 'Invalid credentials'],
                Response::HTTP_UNAUTHORIZED
            );
        } catch (\Throwable $e) {
            return new JsonResponse(
                [
                    'error' => 'An error occurred',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
