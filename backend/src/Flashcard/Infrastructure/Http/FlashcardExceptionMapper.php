<?php

declare(strict_types=1);

namespace App\Flashcard\Infrastructure\Http;

use App\Flashcard\Application\Validation\ValidationFailed;
use App\Flashcard\Domain\Exception\FlashcardNotFound;
use App\Shared\Http\ExceptionMapper;
use App\Shared\Http\HttpProblem;
use Throwable;

final class FlashcardExceptionMapper implements ExceptionMapper
{
  public function map(Throwable $exception): ?HttpProblem
  {
    if ($exception instanceof ValidationFailed) {
      return new HttpProblem(422, 'Validation failed', $exception->fields);
    }

    if ($exception instanceof FlashcardNotFound) {
      return new HttpProblem(404, 'Flashcard not found');
    }

    return null;
  }
}