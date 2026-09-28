<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Flashcard\Domain\Entity\Flashcard;
use App\Flashcard\Infrastructure\Persistence\MysqlFlashcardRepository;
use App\Shared\Database\PdoFactory;
use PDO;
use PHPUnit\Framework\TestCase;

final class MysqlFlashcardRepositoryTest extends TestCase
{
  private PDO $pdo;

  private int $cardCount;

  protected function setUp(): void
  {
    $this->pdo = (new PdoFactory())->create($this->databaseConfig());
    $this->cardCount = (int) $this->pdo->query('SELECT COUNT(*) FROM flashcards')->fetchColumn();
    $this->pdo->beginTransaction();
  }

  protected function tearDown(): void
  {
    if ($this->pdo->inTransaction()) {
      $this->pdo->rollBack();
    }

    $cardCount = (int) $this->pdo->query('SELECT COUNT(*) FROM flashcards')->fetchColumn();
    self::assertSame($this->cardCount, $cardCount);
  }

  public function testSavesUpdatesAndDeletesACard(): void
  {
    $repository = new MysqlFlashcardRepository($this->pdo);
    $card = Flashcard::create('integration front', 'integration back');

    $repository->add($card);

    $stored = $repository->findById($card->id());
    self::assertNotNull($stored);
    self::assertSame('integration front', $stored->front());
    self::assertSame('integration back', $stored->back());

    $stored->update('integration front updated', 'integration back');
    $repository->update($stored);

    $updated = $repository->findById($card->id());
    self::assertNotNull($updated);
    self::assertSame('integration front updated', $updated->front());

    $repository->delete($card->id());
    self::assertNull($repository->findById($card->id()));
  }

  private function databaseConfig(): array
  {
    $config = (require dirname(__DIR__, 2).'/config/settings.php')['db'];

    if ($config['host'] === 'db' && gethostbyname('db') === 'db') {
      $config['host'] = '127.0.0.1';
      $config['port'] = '33066';
    }

    return $config;
  }
}
