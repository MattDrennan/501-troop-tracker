<?php

namespace Tests\Feature\Http\Controllers\Dashboard;

use App\Models\Event;
use App\Models\EventTrooper;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricalTroopsHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_displays_historical_troops_for_authenticated_user(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        $event1 = Event::factory()->closed()->create(['ends_at' => now()->subDays(10)]);
        $event2 = Event::factory()->closed()->create(['ends_at' => now()->subDays(5)]);
        EventTrooper::factory()->for($event1)->for($user)->create();
        EventTrooper::factory()->for($event2)->for($user)->create();

        // Act
        $response = $this->get(route('dashboard.historical-troops-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.historical-troops');
        $response->assertViewHas('historical_troops', function ($collection) use ($event1, $event2)
        {
            return $collection->count() === 2
                && $collection->first()->id === $event2->id // Most recent first
                && $collection->last()->id === $event1->id;
        });
    }

    public function test_invoke_displays_historical_troops_for_another_trooper(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $other_trooper = Trooper::factory()->create();
        $this->actingAs($user);

        $event = Event::factory()->closed()->create();
        EventTrooper::factory()->for($event)->for($other_trooper)->create();

        // Act
        $response = $this->get(route('dashboard.historical-troops-htmx', ['trooper_id' => $other_trooper->id]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.historical-troops');
        $response->assertViewHas('historical_troops', function ($collection) use ($event)
        {
            return $collection->count() === 1 && $collection->first()->id === $event->id;
        });
    }

    public function test_invoke_shows_no_troops_if_none_exist(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get(route('dashboard.historical-troops-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.historical-troops');
        $response->assertViewHas('historical_troops', function ($collection)
        {
            return $collection->isEmpty();
        });
    }
}
