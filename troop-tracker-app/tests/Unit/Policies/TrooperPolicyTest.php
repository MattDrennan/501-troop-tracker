<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Enums\MembershipRole;
use App\Models\Organization;
use App\Models\Trooper;
use App\Policies\TrooperPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrooperPolicyTest extends TestCase
{
    use RefreshDatabase;

    private TrooperPolicy $subject;
    private Trooper $admin_trooper;
    private Trooper $moderator_trooper;
    private Trooper $member_trooper;
    private Trooper $nonmember_trooper;

    protected function setUp(): void
    {
        parent::setUp();

        $unit = Organization::factory()->unit()->create();
        $region = $unit->parent;
        $unit2 = Organization::factory()->unit()->create();

        $this->admin_trooper = Trooper::factory()->create([
            'membership_role' => MembershipRole::Administrator
        ]);

        $this->moderator_trooper = Trooper::factory()
            ->withAssignment($region, moderator: true)
            ->create([
                'membership_role' => MembershipRole::Moderator
            ]);

        $this->member_trooper = Trooper::factory()
            ->withAssignment($unit)
            ->create([
                'membership_role' => MembershipRole::Member
            ]);

        $this->nonmember_trooper = Trooper::factory()
            ->withAssignment($unit2)
            ->create([
                'membership_role' => MembershipRole::Member
            ]);

        $this->subject = new TrooperPolicy();
    }

    public function test_admin_can_view_trooper(): void
    {
        $this->assertTrue($this->subject->view($this->admin_trooper, $this->member_trooper));
    }

    public function test_moderator_can_view_moderated_trooper(): void
    {
        $this->assertTrue($this->subject->view($this->moderator_trooper, $this->member_trooper));
    }

    public function test_moderator_cannot_view_unmoderated_trooper(): void
    {
        $this->assertFalse($this->subject->view($this->moderator_trooper, $this->nonmember_trooper));
    }

    public function test_admin_can_update_trooper(): void
    {
        $this->assertTrue($this->subject->update($this->admin_trooper, $this->member_trooper));
    }

    public function test_moderator_can_update_moderated_trooper(): void
    {
        $this->assertTrue($this->subject->update($this->moderator_trooper, $this->member_trooper));
    }

    public function test_moderator_cannot_update_unmoderated_trooper(): void
    {
        $this->assertFalse($this->subject->update($this->moderator_trooper, $this->nonmember_trooper));
    }

    public function test_admin_can_approve_trooper(): void
    {
        $this->assertTrue($this->subject->approve($this->admin_trooper, $this->member_trooper));
    }

    public function test_moderator_can_approve_moderated_trooper(): void
    {
        $this->assertTrue($this->subject->approve($this->moderator_trooper, $this->member_trooper));
    }

    public function test_moderator_cannot_approve_unmoderated_trooper(): void
    {
        $this->assertFalse($this->subject->approve($this->moderator_trooper, $this->nonmember_trooper));
    }

    public function test_create_is_always_false(): void
    {
        $this->assertFalse($this->subject->create($this->admin_trooper));
        $this->assertFalse($this->subject->create($this->moderator_trooper));
    }

    public function test_delete_is_always_false(): void
    {
        $this->assertFalse($this->subject->delete($this->admin_trooper, $this->member_trooper));
        $this->assertFalse($this->subject->delete($this->moderator_trooper, $this->member_trooper));
    }

    public function test_restore_is_always_false(): void
    {
        $this->assertFalse($this->subject->restore($this->admin_trooper, $this->member_trooper));
        $this->assertFalse($this->subject->restore($this->moderator_trooper, $this->member_trooper));
    }

    public function test_force_delete_is_always_false(): void
    {
        $this->assertFalse($this->subject->forceDelete($this->admin_trooper, $this->member_trooper));
        $this->assertFalse($this->subject->forceDelete($this->moderator_trooper, $this->member_trooper));
    }
}
