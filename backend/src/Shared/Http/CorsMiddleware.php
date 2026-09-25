<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CorsMiddleware implements MiddlewareInterface
{
  public function __construct(
    private readonly string $origin,
    private readonly ResponseFactoryInterface $responseFactory,
  ) {
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    if ($request->getMethod() === 'OPTIONS') {
      return $this->withHeaders($this->responseFactory->createResponse(204));
    }

    return $this->withHeaders($handler->handle($request));
  }

  private function withHeaders(ResponseInterface $response): ResponseInterface
  {
    return $response
      ->withHeader('Access-Control-Allow-Origin', $this->origin)
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept');
  }
}