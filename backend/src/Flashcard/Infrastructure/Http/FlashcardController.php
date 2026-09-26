<?php

declare(strict_types=1);

namespace App\Flashcard\Infrastructure\Http;

use App\Flashcard\Application\UseCase\CreateFlashcard;
use App\Flashcard\Application\UseCase\DeleteFlashcard;
use App\Flashcard\Application\UseCase\GetFlashcard;
use App\Flashcard\Application\UseCase\ListFlashcards;
use App\Flashcard\Application\UseCase\UpdateFlashcard;
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

  public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    return $this->json($response, $this->listFlashcards->handle());
  }

  public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $view = $this->getFlashcard->handle($args['id']);

    return $this->json($response, ['data' => $view->toArray()]);
  }

  public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $view = $this->createFlashcard->handle($request->getParsedBody());

    return $this->json($response, ['data' => $view->toArray()], 201)
      ->withHeader('Location', '/flashcards/'.$view->id);
  }

  public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $view = $this->updateFlashcard->handle($args['id'], $request->getParsedBody());

    return $this->json($response, ['data' => $view->toArray()]);
  }

  public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->deleteFlashcard->handle($args['id']);

    return $response->withStatus(204);
  }

  private function json(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
  {
    $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));

    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
  }
}