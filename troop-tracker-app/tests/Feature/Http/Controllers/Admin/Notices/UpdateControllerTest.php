<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Notices;

use App\Enums\NoticeType;
use App\Models\Notice;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_invoke_returns_view_for_authorized_user(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $this->actingAs($admin);

        $notice = Notice::factory()->create();

        // Act
        $response = $this->get(route('admin.notices.update', ['notice' => $notice]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.notices.update');
        $response->assertViewHas('notice', $notice);
        $response->assertViewHas('options', NoticeType::toDescriptions());
    }

    public function test_invoke_is_inaccessible_by_non_privileged_user(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create(); // A regular user
        $this->actingAs($trooper);

        $notice = Notice::factory()->create();

        // Act
        $response = $this->get(route('admin.notices.update', ['notice' => $notice]));

        // Assert
        $response->assertForbidden();
    }
}