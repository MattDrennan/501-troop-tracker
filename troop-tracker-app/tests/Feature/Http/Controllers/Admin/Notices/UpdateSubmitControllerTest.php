<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Notices;

use App\Enums\NoticeType;
use App\Models\Notice;
use App\Models\Trooper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateSubmitControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_updates_notice_and_redirects_for_authorized_user(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $this->actingAs($admin);

        $notice = Notice::factory()->create([
            'title' => 'Old Title',
            'type' => NoticeType::Info->value,
        ]);

        $new_starts_at = Carbon::now()->addDay()->startOfDay();
        $new_ends_at = Carbon::now()->addWeek()->startOfDay();

        $updated_notice_data = [
            'organization_id' => $notice->organization_id,
            'title' => 'New Updated Title',
            'type' => NoticeType::Warning->value,
            'starts_at' => $new_starts_at->toDateTimeString(),
            'ends_at' => $new_ends_at->toDateTimeString(),
            'message' => 'This is the new updated message.',
        ];

        // Act
        $response = $this->post(
            route('admin.notices.update', ['notice' => $notice]),
            $updated_notice_data
        );

        // Assert
        $response->assertRedirect(route('admin.notices.list'));

        $this->assertDatabaseHas(Notice::class, [
            'id' => $notice->id,
            'title' => 'New Updated Title',
            'type' => NoticeType::Warning->value,
            'message' => 'This is the new updated message.',
        ]);

        // Re-fetch the model to check carbon instance dates
        $notice->refresh();
        $this->assertTrue($new_starts_at->eq($notice->starts_at));
        $this->assertTrue($new_ends_at->eq($notice->ends_at));
    }
}