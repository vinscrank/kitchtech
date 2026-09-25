<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Dto;

use App\Flashcard\Domain\Entity\Flashcard;

final class FlashcardView
{
  public function __construct(
    public readonly string $id,
    public readonly string $front,
    public readonly string $back,
  ) {
  }

  public static function from(Flashcard $flashcard): self
  {
    return new self(
      $flashcard->id()->toString(),
      $flashcard->front(),
      $flashcard->back(),
    );
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'front' => $this->front,
      'back' => $this->back,
    ];
  }
}
