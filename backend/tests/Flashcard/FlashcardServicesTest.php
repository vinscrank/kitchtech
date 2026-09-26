<?php

declare(strict_types=1);

namespace Tests\Flashcard;

use App\Flashcard\Application\UseCase\CreateFlashcard;
use App\Flashcard\Application\UseCase\DeleteFlashcard;
use App\Flashcard\Application\UseCase\GetFlashcard;
use App\Flashcard\Application\UseCase\UpdateFlashcard;
use App\Flashcard\Application\Validation\FlashcardInputValidator;
use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Application\Exception\FlashcardNotFound;
use App\Flashcard\Domain\Exception\InvalidFlashcard;
use App\Flashcard\Domain\Exception\InvalidFlashcardId;
use App\Flashcard\Domain\ValueObject\FlashcardId;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryFlashcardRepository;

final class FlashcardServicesTest extends TestCase
{
  private const MISSING_ID = '00000000-0000-4000-8000-000000000001';

  public function testCreatePersistsFrontAndBack(): void
  {
    $repository = new InMemoryFlashcardRepository();
    $view = (new CreateFlashcard($repository, new FlashcardInputValidator()))
      ->handle(['front' => 'hint', 'back' => 'word']);

    $stored = $repository->findById(FlashcardId::fromString($view->id));

    self::assertNotNull($stored);
    self::assertSame('hint', $stored->front());
    self::assertSame('word', $stored->back());
  }

  public function testGetMissingId(): void
  {
    $this->expectException(FlashcardNotFound::class);
    (new GetFlashcard(new InMemoryFlashcardRepository()))->handle(self::MISSING_ID);
  }

  public function testUpdateMissingId(): void
  {
    $this->expectException(FlashcardNotFound::class);
    (new UpdateFlashcard(new InMemoryFlashcardRepository(), new FlashcardInputValidator()))
      ->handle(self::MISSING_ID, ['front' => 'hint', 'back' => 'word']);
  }

  public function testDeleteMissingId(): void
  {
    $this->expectException(FlashcardNotFound::class);
    (new DeleteFlashcard(new InMemoryFlashcardRepository()))->handle(self::MISSING_ID);
  }

  public function testFromStringRejectsNonUuidV4(): void
  {
    $this->expectException(InvalidFlashcardId::class);
    FlashcardId::fromString('00000000-0000-1000-8000-000000000001');
  }

  public function testCreateRejectsEmptyText(): void
  {
    $this->expectException(InvalidFlashcard::class);
    Flashcard::create('', 'word');
  }
}
