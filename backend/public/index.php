<?php

declare(strict_types=1);

use App\Shared\Http\CorsMiddleware;
use App\Shared\Http\JsonContentTypeMiddleware;
use App\Shared\Http\JsonErrorHandler;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;

require dirname(__DIR__).'/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__).'/config/container.php');
$container = $builder->build();

$app = Bridge::create($container);

$app->add($container->get(JsonContentTypeMiddleware::class));
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$errorMiddleware->setDefaultErrorHandler($container->get(JsonErrorHandler::class));
$app->add($container->get(CorsMiddleware::class));

(require dirname(__DIR__).'/src/Flashcard/Infrastructure/Http/routes.php')($app);

$app->run();
