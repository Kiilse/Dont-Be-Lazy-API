<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Http;

use App\Domain\Shared\Exception\DomainException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Gère les exceptions et les transforme en réponses JSON
 */
final readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof DomainException) {
            return;
        }

        $response = new JsonResponse(
            [
                'error' => $exception->getMessage(),
                'code' => $exception->getErrorCode(),
            ],
            $exception->getHttpStatusCode()
        );

        $event->setResponse($response);
    }
}
