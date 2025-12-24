<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipStatus;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DenialSubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        $pending_trooper = Trooper::factory()->asPending()->create();

        // Act
        $response = $this->post(route('admin.troopers.deny-htmx', $pending_trooper));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_invoke_unauthorized_user_is_forbidden(): void
    {
        // Arrange
        $unauthorized_user = Trooper::factory()->create();
        $pending_trooper = Trooper::factory()->asPending()->create();

        // Act
        $response = $this->actingAs($unauthorized_user)
            ->post(route('admin.troopers.deny-htmx', $pending_trooper));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_denies_trooper_successfully(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $pending_trooper = Trooper::factory()->asPending()->create();

        $expected_message = json_encode([
            'message' => "Trooper {$pending_trooper->name} denied",
            'type' => 'danger',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.troopers.deny-htmx', $pending_trooper));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.approval');
        $response->assertViewHas('trooper', $pending_trooper);
        $response->assertHeader('X-Flash-Message', $expected_message);

        $pending_trooper->refresh();

        $this->assertEquals(MembershipStatus::Denied, $pending_trooper->membership_status);
    }
}
