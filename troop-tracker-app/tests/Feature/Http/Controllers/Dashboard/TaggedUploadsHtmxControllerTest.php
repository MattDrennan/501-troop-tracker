<?php

namespace Tests\Feature\Http\Controllers\Dashboard;

use App\Models\EventUpload;
use App\Models\EventUploadTag;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaggedUploadsHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_displays_tagged_uploads_for_authenticated_user(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        $upload = EventUpload::factory()->create();
        EventUploadTag::factory()->for($user)->create([
            'event_upload_id' => $upload->id
        ]);

        // Act
        $response = $this->get(route('dashboard.tagged-uploads-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.tagged-uploads');
        $response->assertViewHas('uploads', function ($collection) use ($upload)
        {
            return $collection->count() === 1 && $collection->first()->id === $upload->id;
        });
    }

    public function test_invoke_displays_tagged_uploads_for_another_trooper(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $other_trooper = Trooper::factory()->create();
        $this->actingAs($user);

        $upload = EventUpload::factory()->create();
        EventUploadTag::factory()->for($other_trooper)->create([
            'event_upload_id' => $upload->id
        ]);

        // Act
        $response = $this->get(route('dashboard.tagged-uploads-htmx', ['trooper_id' => $other_trooper->id]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.tagged-uploads');
        $response->assertViewHas('uploads', function ($collection) use ($upload)
        {
            return $collection->count() === 1 && $collection->first()->id === $upload->id;
        });
    }

    public function test_invoke_shows_no_uploads_if_none_exist(): void
    {
        // Arrange
        $user = Trooper::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get(route('dashboard.tagged-uploads-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.dashboard.tagged-uploads');
        $response->assertViewHas('uploads', function ($collection)
        {
            return $collection->isEmpty();
        });
    }
}