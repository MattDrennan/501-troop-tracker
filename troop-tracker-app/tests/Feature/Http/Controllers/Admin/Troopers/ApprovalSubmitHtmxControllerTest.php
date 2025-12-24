<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipStatus;
use App\Mail\TrooperApproved;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApprovalSubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        $pending_trooper = Trooper::factory()->asPending()->create();

        // Act
        $response = $this->post(route('admin.troopers.approve-htmx', $pending_trooper));

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
            ->post(route('admin.troopers.approve-htmx', $pending_trooper));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_approves_trooper_and_sends_email_successfully(): void
    {
        // Arrange
        Mail::fake();
        $admin = Trooper::factory()->asAdmin()->create();
        $pending_trooper = Trooper::factory()->asPending()->create();

        $expected_message = json_encode([
            'message' => "Trooper {$pending_trooper->name} approved!",
            'type' => 'success',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.troopers.approve-htmx', $pending_trooper));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.troopers.approval');
        $response->assertViewHas('trooper', $pending_trooper);
        $response->assertHeader('X-Flash-Message', $expected_message);

        $pending_trooper->refresh();

        $this->assertEquals(MembershipStatus::Active, $pending_trooper->membership_status);

        Mail::assertSent(TrooperApproved::class, function (TrooperApproved $mail) use (&$pending_trooper): bool
        {
            return $mail->hasTo($pending_trooper->email);
        });
    }
}