<?php

declare(strict_types=1);

namespace App\Flashcard\Domain\ValueObject;

use App\Flashcard\Domain\Exception\FlashcardNotFound;

final class FlashcardId
{
  private function __construct(private readonly string $value)
  {
  }

  public static function generate(): self
  {
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    $hex = bin2hex($bytes);

    return new self(sprintf(
      '%s-%s-%s-%s-%s',
      substr($hex, 0, 8),
      substr($hex, 8, 4),
      substr($hex, 12, 4),
      substr($hex, 16, 4),
      substr($hex, 20, 12),
    ));
  }

  public static function fromString(string $value): self
  {
    if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value) !== 1) {
      throw new FlashcardNotFound();
    }

    return new self(strtolower($value));
  }

  public function toString(): string
  {
    return $this->value;
  }
}