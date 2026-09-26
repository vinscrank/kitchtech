<?php

declare(strict_types=1);

use App\Flashcard\Infrastructure\Http\FlashcardController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app, FlashcardController $controller): void {
  $app->group('/flashcards', function (RouteCollectorProxy $group) use ($controller): void {
    $group->get('', [$controller, 'index']);
    $group->get('/{id}', [$controller, 'show']);
    $group->post('', [$controller, 'create']);
    $group->put('/{id}', [$controller, 'update']);
    $group->delete('/{id}', [$controller, 'delete']);
  });
};
