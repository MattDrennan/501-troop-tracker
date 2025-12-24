<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Search;

use App\Models\Costume;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostumeSearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_matching_costumes_as_json(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();

        // Costumes for the target organization
        $costume1 = Costume::factory()->for($organization1)->create(['name' => 'Stormtrooper']);
        $costume2 = Costume::factory()->for($organization1)->create(['name' => 'Shadow Stormtrooper']);
        Costume::factory()->for($organization1)->create(['name' => 'Biker Scout']); // Should not match

        // Costume for another organization to ensure scoping works
        Costume::factory()->for($organization2)->create(['name' => 'Stormtrooper']);

        $search_query = 'Storm';

        // Act
        $response = $this->actingAs($trooper)->getJson(route('search.costumes', [
            'query' => $search_query,
            'organization' => $organization1->id,
        ]));

        // Assert
        $response->assertOk();

        // Check that the response contains the two matching costumes from the correct organization
        $response->assertJsonCount(2);
        $response->assertJson([
            [
                'id' => $costume2->id,
                'name' => $costume2->name,
            ],
            [
                'id' => $costume1->id,
                'name' => $costume1->name,
            ],
        ]);

        // Explicitly check that the non-matching costume is not present
        $response->assertJsonMissing(['name' => 'Biker Scout']);
    }
}
