<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Domain\Exception\FlashcardNotFound;
use App\Flashcard\Domain\Repository\FlashcardRepository;
use App\Flashcard\Domain\ValueObject\FlashcardId;

final class InMemoryFlashcardRepository implements FlashcardRepository
{
  private array $items = [];

  public function add(Flashcard $flashcard): void
  {
    $this->items[$flashcard->id()->toString()] = $flashcard;
  }

  public function findById(FlashcardId $id): ?Flashcard
  {
    return $this->items[$id->toString()] ?? null;
  }

  public function update(Flashcard $flashcard): void
  {
    $key = $flashcard->id()->toString();
    if (! isset($this->items[$key])) {
      throw new FlashcardNotFound();
    }

    $this->items[$key] = $flashcard;
  }

  public function delete(FlashcardId $id): void
  {
    unset($this->items[$id->toString()]);
  }

  public function findAll(): array
  {
    $items = array_values($this->items);
    usort($items, fn (Flashcard $a, Flashcard $b): int => strcmp($b->id()->toString(), $a->id()->toString()));

    return $items;
  }
}
