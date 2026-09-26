<?php

declare(strict_types=1);

namespace App\Flashcard\Domain\Exception;

final class InvalidFlashcardId extends \DomainException
{
  public function __construct()
  {
    parent::__construct('Invalid flashcard id');
  }
}
