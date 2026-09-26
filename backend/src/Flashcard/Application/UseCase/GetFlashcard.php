<?php

declare(strict_types=1);

namespace App\Flashcard\Application\UseCase;

use App\Flashcard\Application\Dto\FlashcardView;
use App\Flashcard\Application\Exception\FlashcardNotFound;
use App\Flashcard\Domain\Repository\FlashcardRepository;
use App\Flashcard\Domain\ValueObject\FlashcardId;

final class GetFlashcard
{
  public function __construct(private readonly FlashcardRepository $repository)
  {
  }

  public function handle(string $id): FlashcardView
  {
    $flashcard = $this->repository->findById(FlashcardId::fromString($id));

    if ($flashcard === null) {
      throw new FlashcardNotFound();
    }

    return FlashcardView::from($flashcard);
  }
}