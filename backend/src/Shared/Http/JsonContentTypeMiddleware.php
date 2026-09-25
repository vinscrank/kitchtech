<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonContentTypeMiddleware implements MiddlewareInterface
{
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    $method = $request->getMethod();
    if ($method !== 'POST' && $method !== 'PUT') {
      return $handler->handle($request);
    }

    $mediaType = strtolower(trim(explode(';', $request->getHeaderLine('Content-Type'))[0]));
    if ($mediaType !== 'application/json') {
      throw new HttpProblem(415, 'Content-Type must be application/json');
    }

    $body = $request->getBody();
    $contents = $body->getContents();
    if ($body->isSeekable()) {
      $body->rewind();
    }

    $decoded = json_decode($contents, true);
    if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
      throw new HttpProblem(400, 'Malformed JSON');
    }

    return $handler->handle($request->withParsedBody($decoded));
  }
}
