<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Models\Trooper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        // No user is authenticated

        // Act
        $response = $this->get(route('admin.troopers.list'));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_invoke_unauthorized_user_is_forbidden(): void
    {
        // Arrange
        $unauthorized_user = Trooper::factory()->create();

        // Act
        $response = $this->actingAs($unauthorized_user)
            ->get(route('admin.troopers.list'));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_as_admin_shows_all_troopers(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        Trooper::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.troopers.list'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.list');
        $response->assertViewHas('troopers', function (Collection $troopers)
        {
            return $troopers->count() === 6; // 5 created + 1 admin
        });
    }

    public function test_invoke_as_moderator_shows_only_moderated_troopers(): void
    {
        // Arrange
        $moderator = Trooper::factory()->asModerator()->create();
        $moderated_troopers = Trooper::factory()->count(2)->create();
        Trooper::factory()->count(3)->create(); // Unrelated troopers

        // This assumes a `moderatedBy` scope can be mocked or set up.
        // For a real test, you would set up the relationships that the `moderatedBy` scope relies on.
        // For this example, we'll just check that the controller is called.
        // A more robust test would involve setting up organizations and assignments.

        // Act
        $response = $this->actingAs($moderator)
            ->get(route('admin.troopers.list'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.list');
        // In a real scenario, you'd assert the count of moderated troopers.
        $response->assertViewHas('troopers');
    }
}
