<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Service;

use App\Flashcard\Application\Dto\FlashcardView;
use App\Flashcard\Application\Validation\FlashcardInputValidator;
use App\Flashcard\Domain\Exception\FlashcardNotFound;
use App\Flashcard\Domain\Repository\FlashcardRepository;
use App\Flashcard\Domain\ValueObject\FlashcardId;

final class UpdateFlashcard
{
  public function __construct(
    private readonly FlashcardRepository $repository,
    private readonly FlashcardInputValidator $validator,
  ) {
  }

  public function handle(string $id, array $payload): FlashcardView
  {
    $flashcard = $this->repository->findById(FlashcardId::fromString($id));

    if ($flashcard === null) {
      throw new FlashcardNotFound();
    }

    $input = $this->validator->validate($payload);
    $flashcard->rename($input->front, $input->back);
    $this->repository->update($flashcard);

    return FlashcardView::from($flashcard);
  }
}