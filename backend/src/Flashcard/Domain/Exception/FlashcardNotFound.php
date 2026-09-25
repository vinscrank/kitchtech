<?php

declare(strict_types=1);

namespace App\Flashcard\Domain\Exception;

final class FlashcardNotFound extends \DomainException
{
  public function __construct()
  {
    parent::__construct('Flashcard not found');
  }
}