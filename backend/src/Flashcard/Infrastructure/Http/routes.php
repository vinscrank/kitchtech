<?php

declare(strict_types=1);

use App\Flashcard\Infrastructure\Http\FlashcardController;
use Slim\App;

return function (App $app): void {
  $app->get('/flashcards', [FlashcardController::class, 'index']);
  $app->get('/flashcards/{id}', [FlashcardController::class, 'show']);
  $app->post('/flashcards', [FlashcardController::class, 'create']);
  $app->put('/flashcards/{id}', [FlashcardController::class, 'update']);
  $app->delete('/flashcards/{id}', [FlashcardController::class, 'delete']);
};
