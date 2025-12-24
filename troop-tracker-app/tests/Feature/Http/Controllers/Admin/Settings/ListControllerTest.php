<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Settings;

use App\Models\Setting;
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
        $response = $this->get(route('admin.settings.list'));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_invoke_unauthorized_user_is_forbidden(): void
    {
        // Arrange
        $unauthorized_user = Trooper::factory()->create();

        // Act
        $response = $this->actingAs($unauthorized_user)
            ->get(route('admin.settings.list'));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_authorized_user_can_view_settings(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        Setting::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.settings.list'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.settings.list');
        $response->assertViewHas('settings', function (Collection $settings)
        {
            return $settings->count() === 5;
        });
    }
}