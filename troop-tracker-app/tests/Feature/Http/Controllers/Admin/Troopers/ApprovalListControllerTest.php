<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_invoke_redirects_unauthenticated_users(): void
    {
        // Arrange
        // No user is authenticated

        // Act
        $response = $this->get(route('admin.troopers.approvals'));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_invoke_as_admin_shows_all_pending_troopers(): void
    {
        // Arrange
        $admin_user = Trooper::factory()->asAdmin()->create();
        $pending_troopers = Trooper::factory()->count(3)->asPending()->create();

        // Act
        $response = $this->actingAs($admin_user)->get(route('admin.troopers.approvals'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.approvals');
        $response->assertViewHas('troopers', function ($view_troopers) use ($pending_troopers)
        {
            return $view_troopers->count() === 3 && $view_troopers->pluck('id')->diff($pending_troopers->pluck('id'))->isEmpty();
        });
    }

    public function test_invoke_as_moderator_shows_only_moderated_troopers(): void
    {
        // Arrange
        $unit = Organization::factory()->unit()->create();
        $region = $unit->parent;
        $unit2 = Organization::factory()->unit()->create();

        $moderator = Trooper::factory()
            ->asModerator()
            ->withAssignment($region, moderator: true)
            ->create();

        $member = Trooper::factory()
            ->asPending()
            ->asMember()
            ->withAssignment($unit)
            ->create();

        $nonmember = Trooper::factory()
            ->asPending()
            ->asMember()
            ->withAssignment($unit2)
            ->create();

        // Act
        $response = $this->actingAs($moderator)->get(route('admin.troopers.approvals'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.approvals');
        $response->assertViewHas('troopers', function ($view_troopers)
        {
            return $view_troopers->count() === 1;
        });
    }
}