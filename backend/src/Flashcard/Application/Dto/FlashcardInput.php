<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Dto;

final class FlashcardInput
{
  public function __construct(
    public readonly string $front,
    public readonly string $back,
  ) {
  }
}