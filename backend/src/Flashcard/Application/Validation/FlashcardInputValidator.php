<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Validation;

use App\Flashcard\Application\Dto\FlashcardInput;

final class FlashcardInputValidator
{
  private const MAX_LENGTH = 500;

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

    if ($trimmed === '') {
      $fields[$field] = 'Must not be empty';

      return '';
    }

    if (mb_strlen($trimmed) > self::MAX_LENGTH) {
      $fields[$field] = 'Must be at most 500 characters';

      return '';
    }

    return $trimmed;
  }
}