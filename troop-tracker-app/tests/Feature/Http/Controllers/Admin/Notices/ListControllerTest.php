<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Notices;

use App\Models\Notice;
use App\Models\Organization;
use App\Models\Trooper;
use App\Services\BreadCrumbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_invoke_returns_all_active_notices_for_admin(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $this->actingAs($admin);

        Notice::factory(2)->active()->create();
        Notice::factory()->past()->create(); // Should not be included

        // Act
        $response = $this->get(route('admin.notices.list'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.notices.list');
        $response->assertViewHas('notices', function ($notices)
        {
            return $notices->count() === 2;
        });
    }

    public function test_invoke_returns_only_moderated_notices_for_moderator(): void
    {
        // Arrange
        $unit = Organization::factory()->unit()->create();
        $region = $unit->parent;
        $organization = $region->parent;

        $moderator = Trooper::factory()
            ->asModerator()
            ->withAssignment($region, moderator: true)
            ->create();
        $this->actingAs($moderator);

        $visible_notice = Notice::factory()->active()->withOrganization($unit)->create();
        $invisible_notice = Notice::factory()->active()->withOrganization($organization)->create();

        // Act
        $response = $this->get(route('admin.notices.list'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.notices.list');
        $response->assertViewHas('notices', function ($notices) use ($visible_notice, $invisible_notice)
        {
            return $notices->contains($visible_notice) && !$notices->contains($invisible_notice);
        });
    }

    public function test_invoke_is_inaccessible_by_non_privileged_user(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create(); // A regular user
        $this->actingAs($trooper);

        // Act
        $response = $this->get(route('admin.notices.list'));

        // Assert
        $response->assertForbidden();
    }
}