<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Auth;

use App\Rules\Auth\AtLeastOneOrganizationSelectedRule;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AtLeastOneOrganizationSelectedRuleTest extends TestCase
{
    #[DataProvider('invalidOrganizationDataProvider')]
    public function test_fails_with_invalid_data(mixed $value): void
    {
        // Arrange
        $subject = new AtLeastOneOrganizationSelectedRule();
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
            $this->assertEquals('Please select at least one organization.', $message);
        };

        // Act
        $subject->validate('organizations', $value, $fail);

        // Assert
        $this->assertTrue($fail_was_called, 'The validation rule should have failed but it passed.');
    }

    public static function invalidOrganizationDataProvider(): array
    {
        return [
            'not an array' => ['some_string'],
            'empty array' => [[]],
            'no organization selected' => [[['id' => 1, 'selected' => false]]],
            'selected key is empty string' => [[['id' => 1, 'selected' => '']]],
            'selected key is null' => [[['id' => 1, 'selected' => null]]],
            'selected key is missing' => [[['id' => 1]]],
        ];
    }

    #[DataProvider('validOrganizationDataProvider')]
    public function test_passes_with_valid_data(mixed $value): void
    {
        // Arrange
        $subject = new AtLeastOneOrganizationSelectedRule();
        $fail_was_called = false;
        $fail = function (string $message): void
        {
            $fail_was_called = true;
            $this->fail('The validation rule should have passed but it failed.');
        };

        // Act & Assert
        $subject->validate('organizations', $value, $fail);

        $this->assertFalse($fail_was_called, 'The validation rule should have failed but it passed.');
    }

    public static function validOrganizationDataProvider(): array
    {
        return [
            'one organization selected with bool' => [
                [['id' => 1, 'selected' => true], ['id' => 2, 'selected' => false]],
            ],
            'one organization selected with string 1' => [
                [['id' => 1, 'selected' => '1'], ['id' => 2, 'selected' => '0']],
            ],
            'multiple organizations selected' => [
                [['id' => 1, 'selected' => true], ['id' => 2, 'selected' => true]],
            ],
        ];
    }
}