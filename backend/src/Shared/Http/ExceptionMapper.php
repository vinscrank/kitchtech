<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Throwable;

interface ExceptionMapper
{
  public function map(Throwable $exception): ?HttpProblem;
}