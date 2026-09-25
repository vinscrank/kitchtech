<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Service;

use App\Flashcard\Application\Dto\FlashcardView;
use App\Flashcard\Domain\Exception\FlashcardNotFound;
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