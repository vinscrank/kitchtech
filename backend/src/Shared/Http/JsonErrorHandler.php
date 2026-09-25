<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

final class JsonErrorHandler implements ErrorHandlerInterface
{
  public function __construct(
    private readonly ResponseFactoryInterface $responseFactory,
    private readonly bool $displayErrorDetails,
    private readonly array $mappers,
  ) {
  }

  public function __invoke(
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
  ): ResponseInterface {
    $display = $this->displayErrorDetails || $displayErrorDetails;

    foreach ($this->mappers as $mapper) {
      $mapped = $mapper->map($exception);
      if ($mapped !== null) {
        return $this->respond($mapped->status, $mapped->getMessage(), $mapped->fields);
      }
    }

    if ($exception instanceof HttpProblem) {
      return $this->respond($exception->status, $exception->getMessage(), $exception->fields);
    }

    if ($exception instanceof HttpException) {
      return $this->respond($exception->getCode(), $exception->getMessage());
    }

    $message = $display ? $exception->getMessage() : 'Internal server error';

    return $this->respond(500, $message);
  }

  private function respond(int $status, string $message, array $fields = []): ResponseInterface
  {
    $response = $this->responseFactory->createResponse($status);
    $response->getBody()->write(json_encode([
      'error' => [
        'message' => $message,
        'fields' => $fields,
      ],
    ], JSON_THROW_ON_ERROR));

    return $response->withHeader('Content-Type', 'application/json');
  }
}