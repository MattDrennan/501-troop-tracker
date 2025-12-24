<?php

namespace Tests\Feature\Http\Controllers\Dashboard;

use App\Models\Trooper;
use App\Models\TrooperDonation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationsHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_displays_donations_for_authenticated_user(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        TrooperDonation::factory(3)->for($user)->create();

        // Act
        $response = $this->get(route('dashboard.donations-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.donations');
        $response->assertViewHas('donations', function ($collection)
        {
            return $collection->count() === 3;
        });
    }

    public function test_invoke_displays_donations_for_another_trooper(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $other_trooper = Trooper::factory()->create();
        $this->actingAs($user);

        TrooperDonation::factory()->for($other_trooper)->create();

        // Act
        $response = $this->get(route('dashboard.donations-htmx', ['trooper_id' => $other_trooper->id]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.donations');
        $response->assertViewHas('donations', function ($collection)
        {
            return $collection->count() === 1;
        });
    }

    public function test_invoke_shows_no_donations_if_none_exist(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get(route('dashboard.donations-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.donations');
        $response->assertViewHas('donations', function ($collection)
        {
            return $collection->isEmpty();
        });
    }
}