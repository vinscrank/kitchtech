<?php

declare(strict_types=1);

namespace App\Shared\Database;

use PDO;

final class Migrator
{
  public function __construct(
    private readonly PDO $pdo,
    private readonly string $directory,
  ) {
  }

  public function migrate(): void
  {
    $this->pdo->exec(
      'CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) NOT NULL PRIMARY KEY,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
    );

    $files = glob($this->directory.'/*.sql');
    if ($files === false) {
      return;
    }

    sort($files);
    $applied = $this->applied();

    foreach ($files as $file) {
      $version = basename($file);
      if (isset($applied[$version])) {
        continue;
      }

      $sql = file_get_contents($file);
      if ($sql === false) {
        throw new \RuntimeException('Cannot read migration '.$version);
      }

      foreach ($this->statements($sql) as $statement) {
        $this->pdo->exec($statement);
      }

      $insert = $this->pdo->prepare('INSERT INTO schema_migrations (version) VALUES (:version)');
      $insert->execute(['version' => $version]);
    }
  }

  private function applied(): array
  {
    $applied = [];
    $rows = $this->pdo->query('SELECT version FROM schema_migrations');
    if ($rows === false) {
      return $applied;
    }

    foreach ($rows as $row) {
      $applied[$row['version']] = true;
    }

    return $applied;
  }

  private function statements(string $sql): array
  {
    $withoutComments = preg_replace('/--.*$/m', '', $sql) ?? $sql;
    $statements = [];

    foreach (explode(';', $withoutComments) as $part) {
      $statement = trim($part);
      if ($statement !== '') {
        $statements[] = $statement;
      }
    }

    return $statements;
  }
}