<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Organization;
use App\Models\Trooper;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_returns_true_for_admin(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertTrue($subject->create($admin));
    }

    public function test_create_returns_false_for_non_admin(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertFalse($subject->create($trooper));
    }

    public function test_update_returns_true_for_admin(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertTrue($subject->update($admin, $organization));
    }

    public function test_update_returns_true_for_moderator_of_organization(): void
    {
        // Arrange
        $moderated_organization = Organization::factory()->create();
        $moderator = Trooper::factory()
            ->asModerator()
            ->withAssignment($moderated_organization, moderator: true)
            ->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertTrue($subject->update($moderator, $moderated_organization));
    }

    public function test_update_returns_false_for_moderator_not_of_organization(): void
    {
        // Arrange
        $moderator = Trooper::factory()->asModerator()->create(); // This moderator is not assigned to any organization
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertFalse($subject->update($moderator, $organization));
    }

    public function test_update_returns_false_for_regular_trooper(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertFalse($subject->update($trooper, $organization));
    }

    public function test_moderate_returns_true_for_admin(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertTrue($subject->moderate($admin, $organization));
    }

    public function test_moderate_returns_true_for_moderator_of_organization(): void
    {
        // Arrange
        $moderated_organization = Organization::factory()->create();
        $moderator = Trooper::factory()
            ->asModerator()
            ->withAssignment($moderated_organization, moderator: true)
            ->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertTrue($subject->moderate($moderator, $moderated_organization));
    }

    public function test_moderate_returns_false_for_moderator_not_of_organization(): void
    {
        // Arrange
        $moderator = Trooper::factory()->asModerator()->create(); // This moderator is not assigned to any organization
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertFalse($subject->moderate($moderator, $organization));
    }

    public function test_moderate_returns_false_for_regular_trooper(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertFalse($subject->moderate($trooper, $organization));
    }

    public function test_delete_always_returns_false(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();
        $organization = Organization::factory()->create();
        $subject = new OrganizationPolicy();

        // Act & Assert
        $this->assertFalse($subject->delete($trooper, $organization));
        $this->assertFalse($subject->restore($trooper, $organization));
        $this->assertFalse($subject->forceDelete($trooper, $organization));
    }
}