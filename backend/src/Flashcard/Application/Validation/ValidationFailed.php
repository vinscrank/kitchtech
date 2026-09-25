<?php

declare(strict_types=1);

namespace App\Flashcard\Application\Validation;

final class ValidationFailed extends \RuntimeException
{
  public function __construct(public readonly array $fields)
  {
    parent::__construct('Validation failed');
  }
}