<?php

namespace Tests\Feature\Http\Controllers\Dashboard;

use App\Models\Trooper;
use App\Models\TrooperAward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AwardsHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_displays_awards_for_authenticated_user(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        TrooperAward::factory(3)->for($user)->create();

        // Act
        $response = $this->get(route('dashboard.awards-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.awards');
        $response->assertViewHas('awards', function ($collection)
        {
            return $collection->count() === 3;
        });
    }

    public function test_invoke_displays_awards_for_another_trooper(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $other_trooper = Trooper::factory()->create();
        $this->actingAs($user);

        TrooperAward::factory()->for($other_trooper)->create();

        // Act
        $response = $this->get(route('dashboard.awards-htmx', ['trooper_id' => $other_trooper->id]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.awards');
        $response->assertViewHas('awards', function ($collection)
        {
            return $collection->count() === 1;
        });
    }

    public function test_invoke_shows_no_awards_if_none_exist(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get(route('dashboard.awards-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.awards');
        $response->assertViewHas('awards', function ($collection)
        {
            return $collection->isEmpty();
        });
    }
}
