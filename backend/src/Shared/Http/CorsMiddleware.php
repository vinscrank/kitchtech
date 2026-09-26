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
    private readonly array $origins,
    private readonly ResponseFactoryInterface $responseFactory,
  ) {
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    $origin = $request->getHeaderLine('Origin');
    $allowed = in_array($origin, $this->origins, true) ? $origin : null;

    if ($request->getMethod() === 'OPTIONS') {
      return $this->withHeaders($this->responseFactory->createResponse(204), $allowed);
    }

    return $this->withHeaders($handler->handle($request), $allowed);
  }

  private function withHeaders(ResponseInterface $response, ?string $origin): ResponseInterface
  {
    $response = $response
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept');

    if ($origin === null) {
      return $response;
    }

    return $response->withHeader('Access-Control-Allow-Origin', $origin);
  }
}