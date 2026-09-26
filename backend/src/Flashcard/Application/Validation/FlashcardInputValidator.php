<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Validation;

use App\Flashcard\Application\Dto\FlashcardInput;
use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Domain\Exception\InvalidFlashcard;

final class FlashcardInputValidator
{
  public function validate(array $payload): FlashcardInput
  {
    $fields = [];
    $front = $this->text($payload['front'] ?? null, 'front', $fields);
    $back = $this->text($payload['back'] ?? null, 'back', $fields);

    if ($fields !== []) {
      throw new ValidationFailed($fields);
    }

    return new FlashcardInput($front, $back);
  }

  private function text(mixed $value, string $field, array &$fields): string
  {
    if (! is_string($value)) {
      $fields[$field] = 'Must be a string';

      return '';
    }

    $trimmed = trim($value);

    try {
      Flashcard::guard($trimmed);
    } catch (InvalidFlashcard $exception) {
      $fields[$field] = $exception->getMessage();

      return '';
    }

    return $trimmed;
  }
}