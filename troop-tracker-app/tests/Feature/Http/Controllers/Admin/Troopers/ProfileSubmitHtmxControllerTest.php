<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipStatus;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();

        // Act
        $response = $this->post(route('admin.troopers.profile-htmx', $trooper));

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
            ->post(route('admin.troopers.profile-htmx', $target_trooper));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_updates_profile_successfully(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $target_trooper = Trooper::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $update_data = [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '1234567890',
            'membership_status' => MembershipStatus::Active->value,
        ];

        $expected_message = json_encode([
            'message' => 'Profile updated successfully!',
            'type' => 'success',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.troopers.profile-htmx', $target_trooper), $update_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.profile');
        $response->assertHeader('X-Flash-Message', $expected_message);

        $target_trooper->refresh();
        $this->assertEquals('New Name', $target_trooper->name);
        $this->assertEquals('new@example.com', $target_trooper->email);
    }

    public function test_invoke_with_invalid_data_returns_validation_error(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $target_trooper = Trooper::factory()->create();
        $invalid_data = ['email' => 'not-an-email'];

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.troopers.profile-htmx', $target_trooper), $invalid_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.profile');
        $response->assertViewHas('errors');
        $response->assertHeader('X-Flash-Message');
    }
}
