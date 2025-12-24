<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Admin\AdminDisplayController
 */
class AdminDisplayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        // Act
        $response = $this->get(route('admin.display'));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_authenticated_user_without_permission_is_forbidden(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create(['membership_role' => MembershipRole::Member]);

        // Act
        $response = $this->actingAs($trooper)->get(route('admin.display'));

        // Assert
        $response->assertForbidden();
    }

    #[DataProvider('permissionProvider')]

    public function test_invoke_with_one_pending_approval(string $role): void
    {
        // Arrange
        $admin_trooper = Trooper::factory()->create([
            'membership_role' => $role,
            'membership_status' => MembershipStatus::Active
        ]);
        Trooper::factory()->asPending()->create(); // Pending
        Trooper::factory()->create(['membership_status' => MembershipStatus::Active]); // Approved

        // Act
        $response = $this->actingAs($admin_trooper)->get(route('admin.display'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.display');
        $response->assertViewHas('not_approved', 1);
    }

    #[DataProvider('permissionProvider')]
    public function test_invoke_with_multiple_pending_approvals(string $role): void
    {
        // Arrange
        $admin_trooper = Trooper::factory()->create([
            'membership_role' => $role,
            'membership_status' => MembershipStatus::Active
        ]);
        Trooper::factory()->count(3)->create(['membership_status' => MembershipStatus::Pending]); // Pending

        // Act
        $response = $this->actingAs($admin_trooper)->get(route('admin.display'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.display');
        $response->assertViewHas('not_approved', 3);
    }

    #[DataProvider('permissionProvider')]
    public function test_invoke_with_no_pending_approvals(string $role): void
    {
        // Arrange
        $admin_trooper = Trooper::factory()->create([
            'membership_role' => $role,
            'membership_status' => MembershipStatus::Active
        ]);
        Trooper::factory()->count(2)->create(['membership_status' => MembershipStatus::Active]); // All approved

        // Act
        $response = $this->actingAs($admin_trooper)->get(route('admin.display'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.display');
        $response->assertViewHas('not_approved', 0);
    }

    public static function permissionProvider(): array
    {
        return [
            'admin user' => ['administrator'],
            'moderator user' => ['moderator'],
        ];
    }
}