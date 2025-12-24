<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipRole;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthoritySubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();

        // Act
        $response = $this->post(route('admin.troopers.authority-htmx', $trooper));

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
            ->post(route('admin.troopers.authority-htmx', $target_trooper));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_updates_authority_successfully(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $target_trooper = Trooper::factory()->create(['membership_role' => MembershipRole::Member]);
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();

        // Existing assignment that should be turned off
        TrooperAssignment::factory()->create([
            'trooper_id' => $target_trooper->id,
            'organization_id' => $organization2->id,
            'moderator' => true,
        ]);

        $update_data = [
            'membership_role' => MembershipRole::Moderator->value,
            'moderators' => [
                $organization1->id => ['selected' => '1'], // This one should be turned on
                $organization2->id => ['selected' => '0'], // This one should be turned off
            ],
        ];

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.troopers.authority-htmx', $target_trooper), $update_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.authority');
        $response->assertHeader('X-Flash-Message');

        $target_trooper->refresh();
        $this->assertEquals(MembershipRole::Moderator, $target_trooper->membership_role);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $target_trooper->id,
            'organization_id' => $organization1->id,
            'moderator' => true,
        ]);
        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $target_trooper->id,
            'organization_id' => $organization2->id,
            'moderator' => false,
        ]);
    }
}
