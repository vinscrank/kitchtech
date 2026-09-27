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
    $errors = [];
    $front = $this->text($payload['front'] ?? null, 'front', $errors);
    $back = $this->text($payload['back'] ?? null, 'back', $errors);

    if ($errors !== []) {
      throw new ValidationFailed($errors);
    }

    return new FlashcardInput($front, $back);
  }

  private function text(mixed $value, string $field, array &$errors): string
  {
    if (! is_string($value)) {
      $errors[$field] = 'Must be a string';

      return '';
    }

    $trimmed = trim($value);

    try {
      Flashcard::guard($trimmed);
    } catch (InvalidFlashcard $exception) {
      $errors[$field] = $exception->getMessage();
      return '';
    }

    return $trimmed;
  }
}