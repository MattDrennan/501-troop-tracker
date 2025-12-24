<?php

namespace Tests\Unit\Rules\Auth;

use App\Models\Organization;
use App\Rules\Auth\ValidUnitForRegionRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidUnitForRegionRuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Organization::factory()->unit()->create();
    }

    public function test_validation_passes_when_unit_is_valid_for_region(): void
    {
        // Arrange
        $region = Organization::factory()->region()->create();
        $unit = Organization::factory()->unit()->create(['parent_id' => $region->id]);
        $subject = new ValidUnitForRegionRule($region);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };

        // Act
        $subject->validate('unit_id', $unit->id, $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }

    public function test_validation_fails_when_unit_is_for_another_region(): void
    {
        // Arrange
        $region1 = Organization::factory()->region()->create();
        $region2 = Organization::factory()->region()->create();
        $unit = Organization::factory()->unit()->create(['parent_id' => $region2->id]);
        $subject = new ValidUnitForRegionRule($region1);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
            $this->assertEquals('Unit selection is invalid.', $message);
        };

        // Act
        $subject->validate('unit_id', $unit->id, $fail);

        // Assert
        $this->assertTrue($fail_was_called, 'The validation rule should have failed but it passed.');
    }

    public function test_validation_passes_when_value_is_empty(): void
    {
        // Arrange
        $region = Organization::factory()->region()->create();
        $subject = new ValidUnitForRegionRule($region);
        $fail_was_called = false;
        $fail = function (string $message) use (&$fail_was_called): void
        {
            $fail_was_called = true;
        };

        // Act
        $subject->validate('unit_id', '', $fail);
        $subject->validate('unit_id', null, $fail);

        // Assert
        $this->assertFalse($fail_was_called, 'The validation rule should have passed but it failed.');
    }
}
