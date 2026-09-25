<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Service;

use App\Flashcard\Application\Dto\FlashcardView;
use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Domain\Repository\FlashcardRepository;

final class ListFlashcards
{
  public function __construct(private readonly FlashcardRepository $repository)
  {
  }

  public function handle(): array
  {
    return [
      'data' => array_map(
        fn (Flashcard $flashcard) => FlashcardView::from($flashcard)->toArray(),
        $this->repository->findAll(),
      ),
    ];
  }
}
