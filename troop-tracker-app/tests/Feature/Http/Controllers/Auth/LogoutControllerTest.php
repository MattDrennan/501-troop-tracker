<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_user_out_and_redirects_to_login_page(): void
    {
        // Arrange: Create and authenticate a user.
        $trooper = Trooper::factory()->create();

        $this->actingAs($trooper);

        $this->assertAuthenticatedAs($trooper);

        // Act: Call the logout controller.
        $response = $this->get(route('auth.logout'));

        // Assert: Check that the user is no longer authenticated.
        $this->assertGuest();

        // Assert: Check for the redirect to the login route.
        $response->assertRedirect(route('auth.login', ['logged_out' => '1']));
    }
}
