<?php

declare(strict_types=1);

namespace App\Flashcard\Domain\Entity;

use App\Flashcard\Domain\ValueObject\FlashcardId;

final class Flashcard
{
  private function __construct(
    private readonly FlashcardId $id,
    private string $front,
    private string $back,
  ) {
  }

  public static function create(string $front, string $back): self
  {
    return new self(FlashcardId::generate(), $front, $back);
  }

  public static function reconstitute(FlashcardId $id, string $front, string $back): self
  {
    return new self($id, $front, $back);
  }

  public function rename(string $front, string $back): void
  {
    $this->front = $front;
    $this->back = $back;
  }

  public function id(): FlashcardId
  {
    return $this->id;
  }

  public function front(): string
  {
    return $this->front;
  }

  public function back(): string
  {
    return $this->back;
  }
}
