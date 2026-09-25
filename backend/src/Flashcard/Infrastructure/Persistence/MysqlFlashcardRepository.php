<?php

declare(strict_types=1);

namespace App\Flashcard\Infrastructure\Persistence;

use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Domain\Repository\FlashcardRepository;
use App\Flashcard\Domain\ValueObject\FlashcardId;
use PDO;

final class MysqlFlashcardRepository implements FlashcardRepository
{
  public function __construct(private readonly PDO $pdo)
  {
  }

  public function add(Flashcard $flashcard): void
  {
    $stmt = $this->pdo->prepare('INSERT INTO flashcards (id, front, back) VALUES (?, ?, ?)');
    $stmt->execute([
      $flashcard->id()->toString(),
      $flashcard->front(),
      $flashcard->back(),
    ]);
  }

  public function findById(FlashcardId $id): ?Flashcard
  {
    $stmt = $this->pdo->prepare('SELECT id, front, back FROM flashcards WHERE id = ?');
    $stmt->execute([$id->toString()]);
    $row = $stmt->fetch();

    if ($row === false) {
      return null;
    }

    return $this->map($row);
  }

  public function update(Flashcard $flashcard): void
  {
    $stmt = $this->pdo->prepare('UPDATE flashcards SET front = ?, back = ? WHERE id = ?');
    $stmt->execute([
      $flashcard->front(),
      $flashcard->back(),
      $flashcard->id()->toString(),
    ]);
  }

  public function delete(FlashcardId $id): void
  {
    $stmt = $this->pdo->prepare('DELETE FROM flashcards WHERE id = ?');
    $stmt->execute([$id->toString()]);
  }

  public function findAll(): array
  {
    $stmt = $this->pdo->query('SELECT id, front, back FROM flashcards ORDER BY id DESC');

    return array_map($this->map(...), $stmt->fetchAll());
  }

  private function map(array $row): Flashcard
  {
    return Flashcard::reconstitute(
      FlashcardId::fromString($row['id']),
      $row['front'],
      $row['back'],
    );
  }
}
