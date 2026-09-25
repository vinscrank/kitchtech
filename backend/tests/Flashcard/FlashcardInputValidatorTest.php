<?php

declare(strict_types=1);

namespace Tests\Flashcard;

use App\Flashcard\Application\Validation\FlashcardInputValidator;
use App\Flashcard\Application\Validation\ValidationFailed;
use PHPUnit\Framework\TestCase;

final class FlashcardInputValidatorTest extends TestCase
{
  private FlashcardInputValidator $validator;

  protected function setUp(): void
  {
    $this->validator = new FlashcardInputValidator();
  }

  public function testRejectsEmptyFront(): void
  {
    $this->assertFieldError(['front' => '', 'back' => 'answer'], 'front', 'Must not be empty');
  }

  public function testRejectsBlankBack(): void
  {
    $this->assertFieldError(['front' => 'hint', 'back' => '   '], 'back', 'Must not be empty');
  }

  public function testRejectsFrontLongerThan500(): void
  {
    $this->assertFieldError(
      ['front' => str_repeat('a', 501), 'back' => 'answer'],
      'front',
      'Must be at most 500 characters',
    );
  }

  public function testTrimsAndKeepsTheTwoFields(): void
  {
    $input = $this->validator->validate(['front' => '  hint  ', 'back' => ' word ', 'extra' => 'ignored']);

    self::assertSame('hint', $input->front);
    self::assertSame('word', $input->back);
  }

  private function assertFieldError(array $payload, string $field, string $message): void
  {
    try {
      $this->validator->validate($payload);
      self::fail('Expected a validation error');
    } catch (ValidationFailed $exception) {
      self::assertSame($message, $exception->fields[$field]);
    }
  }
}
