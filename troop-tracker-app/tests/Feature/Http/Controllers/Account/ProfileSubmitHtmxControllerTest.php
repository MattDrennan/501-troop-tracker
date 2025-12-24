<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Account;

use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        // Act
        $response = $this->post(route('account.profile-htmx'));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_authenticated_user_can_update_their_profile(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '111-111-1111',
        ]);

        $new_profile_data = [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '2222222222',
        ];

        // Act
        $response = $this->actingAs($trooper)
            ->post(route('account.profile-htmx'), $new_profile_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.profile');
        $response->assertViewHasAll($new_profile_data);

        $expected_flash = json_encode([
            'message' => 'Profile updated successfully!',
            'type' => 'success',
        ]);
        $response->assertHeader('X-Flash-Message', $expected_flash);

        $this->assertDatabaseHas(Trooper::class, [
            'id' => $trooper->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '2222222222',
        ]);
    }

    public function test_validation_failure_returns_view_with_errors(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $invalid_data = [
            'name' => '', // Name is required
            'email' => 'not-an-email',
            'phone' => '123-456-7890',
        ];

        // Act
        $response = $this->actingAs($trooper)
            ->post(route('account.profile-htmx'), $invalid_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.profile');
        $response->assertViewHas('errors');
        $response->assertViewHasAll([
            'name' => '',
            'email' => 'not-an-email',
            'phone' => '1234567890',
        ]);

        $expected_flash = json_encode([
            'message' => 'Please fix the validation errors',
            'type' => 'danger',
        ]);
        $response->assertHeader('X-Flash-Message', $expected_flash);

        $this->assertDatabaseHas(Trooper::class, [
            'id' => $trooper->id,
            'name' => 'Old Name', // Assert data was not changed
            'email' => 'old@example.com',
        ]);
    }
}