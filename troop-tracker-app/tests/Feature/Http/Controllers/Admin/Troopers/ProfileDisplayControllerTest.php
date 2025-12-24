<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileDisplayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();

        // Act
        $response = $this->get(route('admin.troopers.update', $trooper));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_invoke_unauthorized_user_is_forbidden(): void
    {
        // Arrange
        $unauthorized_user = Trooper::factory()->create();
        $target_trooper = Trooper::factory()->create();

        // Act
        $response = $this->actingAs($unauthorized_user)
            ->get(route('admin.troopers.update', $target_trooper));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_authorized_user_can_view_profile(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $target_trooper = Trooper::factory()->create();

        // Act
        $response = $this->actingAs($admin)
            ->get(route('admin.troopers.update', $target_trooper));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.update');
        $response->assertViewHas('trooper', function (Trooper $view_trooper) use ($target_trooper): bool
        {
            return $view_trooper->id === $target_trooper->id;
        });
    }
}