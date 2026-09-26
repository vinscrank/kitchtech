<?php

declare(strict_types=1);

use App\Flashcard\Application\UseCase\CreateFlashcard;
use App\Flashcard\Application\UseCase\DeleteFlashcard;
use App\Flashcard\Application\UseCase\GetFlashcard;
use App\Flashcard\Application\UseCase\ListFlashcards;
use App\Flashcard\Application\UseCase\UpdateFlashcard;
use App\Flashcard\Application\Validation\FlashcardInputValidator;
use App\Flashcard\Infrastructure\Http\FlashcardController;
use App\Flashcard\Infrastructure\Http\FlashcardExceptionMapper;
use App\Flashcard\Infrastructure\Persistence\MysqlFlashcardRepository;
use App\Shared\Database\PdoFactory;
use App\Shared\Http\CorsMiddleware;
use App\Shared\Http\JsonContentTypeMiddleware;
use App\Shared\Http\JsonErrorHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

require dirname(__DIR__).'/vendor/autoload.php';

$settings = require dirname(__DIR__).'/config/settings.php';
$responses = new ResponseFactory();


$repository = new MysqlFlashcardRepository((new PdoFactory())->create($settings['db']));
$validator = new FlashcardInputValidator();
$controller = new FlashcardController(
  new ListFlashcards($repository),
  new GetFlashcard($repository),
  new CreateFlashcard($repository, $validator),
  new UpdateFlashcard($repository, $validator),
  new DeleteFlashcard($repository),
);

$app = AppFactory::create();
$app->add(new JsonContentTypeMiddleware());
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$errorMiddleware->setDefaultErrorHandler(new JsonErrorHandler(
  $responses,
  $settings['debug'],
  [new FlashcardExceptionMapper()],
));
$app->add(new CorsMiddleware($settings['corsOrigins'], $responses));

(require dirname(__DIR__).'/src/Flashcard/Infrastructure/Http/routes.php')($app, $controller);

$app->run();
