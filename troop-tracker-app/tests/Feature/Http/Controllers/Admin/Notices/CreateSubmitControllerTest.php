<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Notices;

use App\Enums\NoticeType;
use App\Models\Notice;
use App\Models\Organization;
use App\Models\Trooper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSubmitControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_creates_notice_and_redirects_for_authorized_user(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $this->actingAs($admin);

        $organization = Organization::factory()->create();
        $starts_at = Carbon::now()->toDateTimeString();
        $ends_at = Carbon::now()->addWeek()->toDateTimeString();

        $notice_data = [
            'organization_id' => $organization->id,
            'title' => 'Test Notice Title',
            'type' => NoticeType::Info->value,
            'starts_at' => $starts_at,
            'ends_at' => $ends_at,
            'message' => 'This is a test notice message.',
        ];

        // Act
        $response = $this->post(route('admin.notices.create'), $notice_data);

        // Assert
        $response->assertRedirect(route('admin.notices.list'));

        $this->assertDatabaseHas(Notice::class, [
            'organization_id' => $organization->id,
            'title' => 'Test Notice Title',
            'type' => NoticeType::Info->value,
            'message' => 'This is a test notice message.',
        ]);

        // Retrieve the created notice to check dates, as they are cast
        $created_notice = Notice::where('title', 'Test Notice Title')->first();
        $this->assertNotNull($created_notice);
        $this->assertEquals(Carbon::parse($starts_at)->toDateTimeString(), $created_notice->starts_at->toDateTimeString());
        $this->assertEquals(Carbon::parse($ends_at)->toDateTimeString(), $created_notice->ends_at->toDateTimeString());
    }
}