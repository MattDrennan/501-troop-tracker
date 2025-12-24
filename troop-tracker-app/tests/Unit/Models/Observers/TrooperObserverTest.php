<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Observers;

use App\Models\Trooper;
use App\Models\TrooperAchievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Observers\TrooperObserver
 */
class TrooperObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_event_creates_trooper_achievement_record(): void
    {
        // Arrange: A trooper is created, which will trigger the observer.

        // Act
        $trooper = Trooper::factory()->create();

        // Assert: Check that a TrooperAchievement record was created for the new trooper.
        $this->assertDatabaseHas(TrooperAchievement::class, [
            'trooper_id' => $trooper->id,
        ]);
        $this->assertDatabaseCount('tt_trooper_achievements', 1);
    }
}