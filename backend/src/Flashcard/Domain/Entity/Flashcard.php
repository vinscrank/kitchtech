<?php

declare(strict_types=1);

namespace App\Flashcard\Domain\Entity;

use App\Flashcard\Domain\Exception\InvalidFlashcard;
use App\Flashcard\Domain\ValueObject\FlashcardId;

final class Flashcard
{
  private function __construct(
    private readonly FlashcardId $id,
    private string $front,
    private string $back,
  ) {
    self::guard($front);
    self::guard($back);
  }

  public static function create(string $front, string $back): self
  {
    return new self(FlashcardId::generate(), $front, $back);
  }

  public static function reconstitute(FlashcardId $id, string $front, string $back): self
  {
    return new self($id, $front, $back);
  }

  public function update(string $front, string $back): void
  {
    self::guard($front);
    self::guard($back);
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

  public static function guard(string $value): void
  {
    if ($value === '') {
      throw new InvalidFlashcard('Must not be empty');
    }

    if (mb_strlen($value) > 500) {
      throw new InvalidFlashcard('Must be at most 500 characters');
    }
  }
}
