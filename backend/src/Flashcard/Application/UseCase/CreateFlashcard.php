<?php

declare(strict_types=1);

namespace App\Flashcard\Application\UseCase;

use App\Flashcard\Application\Dto\FlashcardView;
use App\Flashcard\Application\Validation\FlashcardInputValidator;
use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Domain\Repository\FlashcardRepository;

final class CreateFlashcard
{
  public function __construct(
    private readonly FlashcardRepository $repository,
    private readonly FlashcardInputValidator $validator,
  ) {
  }

  public function handle(array $payload): FlashcardView
  {
    $input = $this->validator->validate($payload);
    $flashcard = Flashcard::create($input->front, $input->back);
    $this->repository->add($flashcard);

    return FlashcardView::from($flashcard);
  }
}