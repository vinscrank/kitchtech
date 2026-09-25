<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Service;

use App\Flashcard\Domain\Exception\FlashcardNotFound;
use App\Flashcard\Domain\Repository\FlashcardRepository;
use App\Flashcard\Domain\ValueObject\FlashcardId;

final class DeleteFlashcard
{
  public function __construct(private readonly FlashcardRepository $repository)
  {
  }

  public function handle(string $id): void
  {
    $flashcardId = FlashcardId::fromString($id);

    if ($this->repository->findById($flashcardId) === null) {
      throw new FlashcardNotFound();
    }

    $this->repository->delete($flashcardId);
  }
}
