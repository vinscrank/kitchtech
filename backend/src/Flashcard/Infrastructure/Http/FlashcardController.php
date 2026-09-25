<?php

declare(strict_types=1);

namespace App\Flashcard\Infrastructure\Http;

use App\Flashcard\Application\Service\CreateFlashcard;
use App\Flashcard\Application\Service\DeleteFlashcard;
use App\Flashcard\Application\Service\GetFlashcard;
use App\Flashcard\Application\Service\ListFlashcards;
use App\Flashcard\Application\Service\UpdateFlashcard;
use App\Shared\Http\HttpProblem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class FlashcardController
{
  public function __construct(
    private readonly ListFlashcards $listFlashcards,
    private readonly GetFlashcard $getFlashcard,
    private readonly CreateFlashcard $createFlashcard,
    private readonly UpdateFlashcard $updateFlashcard,
    private readonly DeleteFlashcard $deleteFlashcard,
  ) {
  }

  public function index(ResponseInterface $response): ResponseInterface
  {
    return $this->json($response, $this->listFlashcards->handle());
  }

  public function show(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
  {
    $view = $this->getFlashcard->handle($id);

    return $this->json($response, ['data' => $view->toArray()]);
  }

  public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
  {
    $view = $this->createFlashcard->handle($this->payload($request));

    return $this->json($response, ['data' => $view->toArray()], 201)
      ->withHeader('Location', '/flashcards/'.$view->id);
  }

  public function update(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
  {
    $view = $this->updateFlashcard->handle($id, $this->payload($request));

    return $this->json($response, ['data' => $view->toArray()]);
  }

  public function delete(ServerRequestInterface $request, ResponseInterface $response, string $id): ResponseInterface
  {
    $this->deleteFlashcard->handle($id);

    return $response->withStatus(204);
  }

  private function payload(ServerRequestInterface $request): array
  {
    $parsed = $request->getParsedBody();

    if (! is_array($parsed)) {
      throw new HttpProblem(400, 'Malformed JSON');
    }

    return $parsed;
  }

  private function json(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
  {
    $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));

    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
  }
}