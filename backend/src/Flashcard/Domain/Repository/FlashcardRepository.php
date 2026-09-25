<?php

declare(strict_types=1);

namespace App\Flashcard\Domain\Repository;

use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Domain\ValueObject\FlashcardId;

interface FlashcardRepository
{
  public function add(Flashcard $flashcard): void;

  public function findById(FlashcardId $id): ?Flashcard;

  public function update(Flashcard $flashcard): void;

  public function delete(FlashcardId $id): void;

  public function findAll(): array;
}