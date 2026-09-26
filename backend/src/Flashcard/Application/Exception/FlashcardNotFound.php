<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Exception;

final class FlashcardNotFound extends \RuntimeException
{
  public function __construct()
  {
    parent::__construct('Flashcard not found');
  }
}
