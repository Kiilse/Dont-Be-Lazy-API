<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtAuthenticationSuccessHandler implements EventSubscriberInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $domainUser = $user->getDomainUser();

        $data['user'] = [
            'id' => $domainUser->id()->value(),
            'email' => $domainUser->email()->value(),
            'name' => $domainUser->name(),
            'role' => $domainUser->role()->value,
        ];

        $event->setData($data);
    }
}
