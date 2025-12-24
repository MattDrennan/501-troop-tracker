<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Admin\Organizations;

use App\Models\Organization;
use App\Rules\Admin\Organizations\UniqueNameRule;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueNameRuleTest extends TestCase
{
    use RefreshDatabase;

    private Organization $parent_organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parent_organization = Organization::factory()->create();
    }

    public function test_validate_passes_for_unique_name_on_create(): void
    {
        // Arrange
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };
        $subject = new UniqueNameRule(false, $this->parent_organization);

        // Act
        $subject->validate('name', 'Unique Name', $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }

    public function test_validate_fails_for_duplicate_name_on_create(): void
    {
        // Arrange
        Organization::factory()->create(['parent_id' => $this->parent_organization->id, 'name' => 'Existing Name']);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };
        $subject = new UniqueNameRule(false, $this->parent_organization);

        // Act
        $subject->validate('name', 'Existing Name', $fail);

        // Assert
        $this->assertTrue($fail_was_called);
    }

    public function test_validate_passes_for_unique_name_on_update(): void
    {
        // Arrange
        $organization_to_update = Organization::factory()->create(['parent_id' => $this->parent_organization->id]);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };
        $subject = new UniqueNameRule(true, $organization_to_update);

        // Act
        $subject->validate('name', 'A New Unique Name', $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }

    public function test_validate_fails_for_duplicate_name_on_update(): void
    {
        // Arrange
        Organization::factory()->create(['parent_id' => $this->parent_organization->id, 'name' => 'Existing Sibling Name']);
        $organization_to_update = Organization::factory()->create(['parent_id' => $this->parent_organization->id]);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };
        $subject = new UniqueNameRule(true, $organization_to_update);

        // Act
        $subject->validate('name', 'Existing Sibling Name', $fail);

        // Assert
        $this->assertTrue($fail_was_called);
    }

    public function test_validate_passes_when_name_is_unchanged_on_update(): void
    {
        // Arrange
        $organization_to_update = Organization::factory()->create([
            'parent_id' => $this->parent_organization->id,
            'name' => 'Original Name'
        ]);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };
        $subject = new UniqueNameRule(true, $organization_to_update);

        // Act
        $subject->validate('name', 'Original Name', $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }
}