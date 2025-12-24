<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Observers;

use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_event_assigns_correct_node_path(): void
    {
        // Arrange
        $parent = Organization::factory()->create();

        // Act
        $child = Organization::factory()->create(['parent_id' => $parent->id]);

        // Assert
        $this->assertSame("{$parent->id}:{$child->id}:", $child->node_path);
        $this->assertEquals(2, $child->depth);
    }

    public function test_created_event_assigns_correct_node_path_for_root_node(): void
    {
        // Act
        $organization = Organization::factory()->create(['parent_id' => null]);

        // Assert
        $this->assertSame($organization->id . ':', $organization->node_path);
        $this->assertEquals(1, $organization->depth);
    }

    public function test_updating_event_reassigns_correct_node_path(): void
    {
        // Arrange
        $original_parent = Organization::factory()->create();
        $new_parent = Organization::factory()->create();
        $child = Organization::factory()->create(['parent_id' => $original_parent->id]);

        // Pre-Assert
        $this->assertSame("{$original_parent->id}:{$child->id}:", $child->node_path);

        // Act
        $child = Organization::find($child->id);
        $child->parent_id = $new_parent->id;
        $child->save();

        // Assert
        $this->assertSame("{$new_parent->id}:{$child->id}:", $child->node_path);
        $this->assertEquals(2, $child->depth);
    }
}
