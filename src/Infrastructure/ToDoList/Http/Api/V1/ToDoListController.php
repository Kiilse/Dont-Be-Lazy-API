<?php

declare(strict_types=1);

namespace App\Infrastructure\ToDoList\Http\Api\V1;

use Symfony\Component\Routing\Attribute\Route;
use App\Application\ToDoList\Command\CreateToDoList\CreateToDoListCommandHandler;
use App\Application\ToDoList\Command\CreateToDoList\CreateToDoListCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\Shared\Exception\DomainException;
use App\Application\ToDoList\Query\GetToDoList\GetToDoListQuery;
use App\Application\ToDoList\Query\GetToDoList\GetToDoListQueryHandler;

#[Route('/api/v1/to-do-lists', name: 'api_v1_to_do_lists_')]
final readonly class ToDoListController
{
    public function __construct(
        private CreateToDoListCommandHandler $createToDoListHandler,
        private GetToDoListQueryHandler $getToDoListHandler,
        private ValidatorInterface $validator
    ) {}

    /**
     * POST /api/v1/to-do-lists
     * Create a new to-do list
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

        $constraints = new Collection([
            'userId' => [new NotBlank(), new Uuid()],
            'title' => [new NotBlank(), new Length(min: 1, max: 255)],
            'mode' => [new NotBlank(), new Choice(['CLASSIQUE', 'TEMPO'])],
            'timerType' => [new NotBlank(), new Choice(['FIX', 'FLEX', 'CHRONO'])],
            'timerValue' => [new NotBlank(), new Positive()]
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
            $command = new CreateToDoListCommand(
                userId: $data['userId'],
                title: $data['title'],
                mode: $data['mode'],
                timerType: $data['timerType'],
                timerValue: $data['timerValue']
            );

            $toDoListId = ($this->createToDoListHandler)($command);

            return new JsonResponse(
                ['id' => $toDoListId->value()],
                Response::HTTP_CREATED,
                ['Location' => "/api/v1/to-do-lists/{$toDoListId->value()}"]
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

    /**
     * GET /api/v1/to-do-lists/{id}
     * Get to-do list by id
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        if (empty(trim($id))) {
            return new JsonResponse(
                ['error' => 'To-do list ID is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $query = new GetToDoListQuery(toDoListId: $id);
            $toDoList = ($this->getToDoListHandler)($query);

            return new JsonResponse($toDoList->jsonSerialize());
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
