<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Http\Api\V1;

use App\Application\User\DTO\UserResponseDTO;
use App\Application\User\Query\GetUser\GetUserQuery;
use App\Application\User\Query\GetUser\GetUserQueryHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller d'authentification
 */
#[Route('/api/v1/auth', name: 'api_v1_auth_')]
final readonly class AuthController
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private GetUserQueryHandler $getUserHandler
    ) {}

    /**
     * GET /api/v1/auth/me
     * Récupère les informations de l'utilisateur connecté
     */
    #[Route('/me', name: 'me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(Request $request): JsonResponse
    {
        $user = $request->getUser();

        if (!$user instanceof \App\Infrastructure\User\Security\User) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $domainUser = $user->getDomainUser();
        $query = new GetUserQuery(userId: $domainUser->id()->value());
        $userDto = ($this->getUserHandler)($query);

        return new JsonResponse($userDto->jsonSerialize());
    }
}
