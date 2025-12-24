<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Observers;

use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrooperAssignmentObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_event_throws_exception_for_member_in_parent_organization(): void
    {
        // Assert
        $this->expectException(Exception::class);

        // Arrange
        $unit = Organization::factory()->unit()->create();
        $trooper = Trooper::factory()->create();

        // Act
        TrooperAssignment::factory()->create([
            'trooper_id' => $trooper->id,
            'organization_id' => $unit->parent_id,
            'member' => true,
        ]);
    }

    public function test_created_event_allows_member_in_leaf_organization(): void
    {
        // Arrange
        $leaf_organization = Organization::factory()->create(); // No children
        $trooper = Trooper::factory()->create();

        // Act
        $assignment = TrooperAssignment::factory()->create([
            'trooper_id' => $trooper->id,
            'organization_id' => $leaf_organization->id,
            'member' => true,
        ]);

        // Assert
        $this->assertDatabaseHas(TrooperAssignment::class, [
            'id' => $assignment->id,
        ]);
    }

    public function test_created_event_allows_non_member_in_parent_organization(): void
    {
        // Arrange
        $parent_organization = Organization::factory()->create();
        Organization::factory()->create(['parent_id' => $parent_organization->id]); // Child organization
        $trooper = Trooper::factory()->create();

        // Act
        // Create a non-member (e.g., moderator) assignment
        $assignment = TrooperAssignment::factory()->create([
            'trooper_id' => $trooper->id,
            'organization_id' => $parent_organization->id,
            'member' => false,
            'moderator' => true,
        ]);

        // Assert
        $this->assertDatabaseHas(TrooperAssignment::class, [
            'id' => $assignment->id,
        ]);
    }
}