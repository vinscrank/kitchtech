<?php

declare(strict_types=1);

namespace Tests\Flashcard;

use App\Flashcard\Application\Validation\FlashcardInputValidator;
use App\Flashcard\Application\Validation\ValidationFailed;
use PHPUnit\Framework\TestCase;

final class FlashcardInputValidatorTest extends TestCase
{
  private FlashcardInputValidator $inputValidator;

  protected function setUp(): void
  {
    $this->inputValidator = new FlashcardInputValidator();
  }

  public function testRejectsEmptyFront(): void
  {
    $this->expectValidationError(['front' => '', 'back' => 'answer'], 'front', 'Must not be empty');
  }

  public function testRejectsBlankBack(): void
  {
    $this->expectValidationError(['front' => 'hint', 'back' => '   '], 'back', 'Must not be empty');
  }

  public function testRejectsFrontLongerThan500(): void
  {
    $this->expectValidationError(
      ['front' => str_repeat('a', 501), 'back' => 'answer'],
      'front',
      'Must be at most 500 characters',
    );
  }

  public function testTrimsAndKeepsTheTwoFields(): void
  {
    $validatedInput = $this->inputValidator->validate(['front' => '  hint  ', 'back' => ' word ', 'extra' => 'ignored']);

    self::assertSame('hint', $validatedInput->front);
    self::assertSame('word', $validatedInput->back);
  }

  private function expectValidationError(array $requestBody, string $fieldName, string $expectedMessage): void
  {
    try {
      $this->inputValidator->validate($requestBody);
      self::fail('Expected a validation error');
    } catch (ValidationFailed $validationError) {
      self::assertSame($expectedMessage, $validationError->fields[$fieldName]);
    }
  }
}
