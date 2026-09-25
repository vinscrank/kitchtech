<?php

declare(strict_types=1);

use App\Flashcard\Domain\Repository\FlashcardRepository;
use App\Flashcard\Infrastructure\Http\FlashcardExceptionMapper;
use App\Flashcard\Infrastructure\Persistence\MysqlFlashcardRepository;
use App\Shared\Database\PdoFactory;
use App\Shared\Http\CorsMiddleware;
use App\Shared\Http\JsonErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;

$settings = require __DIR__.'/settings.php';

return [
  ResponseFactoryInterface::class => \DI\autowire(ResponseFactory::class),
  PDO::class => static function () use ($settings): PDO {
    return (new PdoFactory())->create($settings['db']);
  },
  FlashcardRepository::class => \DI\autowire(MysqlFlashcardRepository::class),
  CorsMiddleware::class => \DI\autowire()->constructorParameter('origin', $settings['corsOrigin']),
  JsonErrorHandler::class => static function (ContainerInterface $container) use ($settings): JsonErrorHandler {
    return new JsonErrorHandler(
      $container->get(ResponseFactoryInterface::class),
      $settings['debug'],
      [$container->get(FlashcardExceptionMapper::class)],
    );
  },
];
