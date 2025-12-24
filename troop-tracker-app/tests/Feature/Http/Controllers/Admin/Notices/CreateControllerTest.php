<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Notices;

use App\Models\Notice;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_invoke_returns_view_without_organization(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $this->actingAs($admin);

        // Act
        $response = $this->get(route('admin.notices.create'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.notices.create');
        $response->assertViewHas('notice', function ($notice)
        {
            return $notice instanceof Notice && $notice->organization_id === null;
        });
    }

    public function test_invoke_preloads_organization_for_admin(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $this->actingAs($admin);
        $organization = Organization::factory()->create();

        // Act
        $response = $this->get(route('admin.notices.create', ['organization_id' => $organization->id]));

        // Assert
        $response->assertOk();
        $response->assertViewHas('notice', function ($notice) use ($organization)
        {
            return $notice instanceof Notice && $notice->organization->id === $organization->id;
        });
    }

    public function test_invoke_preloads_organization_for_moderator(): void
    {
        // Arrange
        $moderated_org = Organization::factory()->create();
        $moderator = Trooper::factory()
            ->asModerator()
            ->withAssignment($moderated_org, moderator: true)
            ->create();
        $this->actingAs($moderator);

        // Act
        $response = $this->get(route('admin.notices.create', ['organization_id' => $moderated_org->id]));

        // Assert
        $response->assertOk();
        $response->assertViewHas('notice', function ($notice) use ($moderated_org)
        {
            return $notice instanceof Notice && $notice->organization->id === $moderated_org->id;
        });
    }

    public function test_invoke_fails_for_moderator_on_unmoderated_organization(): void
    {
        // Arrange
        $unmoderated_org = Organization::factory()->create();
        $moderator = Trooper::factory()->asModerator()->create(); // No assignments
        $this->actingAs($moderator);

        // Act
        $response = $this->get(route('admin.notices.create', ['organization_id' => $unmoderated_org->id]));

        // Assert
        $response->assertNotFound();
    }

    public function test_invoke_is_inaccessible_by_non_privileged_user(): void
    {
        // Arrange

        $trooper = Trooper::factory()->create(); // A regular user
        $this->actingAs($trooper);

        // Act
        $response = $this->get(route('admin.notices.create'));

        // Assert
        $response->assertForbidden();
    }
}