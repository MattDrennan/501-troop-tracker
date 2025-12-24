<?php

namespace Tests\Unit\Rules\Auth;

use App\Models\Organization;
use App\Models\Trooper;
use App\Rules\Auth\UniqueOrganizationIdentifierRule;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UniqueOrganizationIdentifierRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_passes_when_identifier_is_unique(): void
    {
        // Arrange
        $organization = Organization::factory()->create();
        $subject = new UniqueOrganizationIdentifierRule($organization);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };

        // Act
        $subject->validate('identifier', 'TK-12345', $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }

    public function test_validation_fails_when_identifier_is_not_unique(): void
    {
        // Arrange
        $organization = Organization::factory()->create();
        $trooper = Trooper::factory()->create();
        $organization->troopers()->attach($trooper, ['identifier' => 'TK-12345']);

        $subject = new UniqueOrganizationIdentifierRule($organization);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called, $organization): void
        {
            $fail_was_called = true;
            $this->assertEquals("{$organization->name} ID already exists.", $message);
        };

        // Act
        $subject->validate('identifier', 'TK-12345', $fail);
    }

    public function test_validation_passes_when_identifier_is_empty(): void
    {
        // Arrange
        $organization = Organization::factory()->create();
        $subject = new UniqueOrganizationIdentifierRule($organization);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };

        // Act
        $subject->validate('identifier', '', $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }

    public function test_validation_does_not_fail_for_other_organizations(): void
    {
        // Arrange
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        $trooper = Trooper::factory()->create();

        // Attach the identifier to a different organization
        $organization2->troopers()->attach($trooper, ['identifier' => 'TK-12345']);

        $subject = new UniqueOrganizationIdentifierRule($organization1);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };

        // Act
        $subject->validate('identifier', 'TK-12345', $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }
}
