<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Scopes;

use App\Enums\MembershipStatus;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasTrooperScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_by_username_scope_returns_correct_trooper(): void
    {
        // Arrange
        $trooper1 = Trooper::factory()->create(['username' => 'testuser1']);
        $trooper2 = Trooper::factory()->create(['username' => 'testuser2']);

        // Act
        $result = Trooper::byUsername('testuser1')->first();

        // Assert
        $this->assertNotNull($result);
        $this->assertTrue($result->is($trooper1));
        $this->assertFalse($result->is($trooper2));
    }

    public function test_pending_approvals_scope_returns_only_pending_troopers_ordered_by_name(): void
    {
        // Arrange
        $pending_trooper_b = Trooper::factory()->create(['name' => 'Beta', 'membership_status' => MembershipStatus::Pending]);
        $active_trooper = Trooper::factory()->create(['membership_status' => MembershipStatus::Active]);
        $pending_trooper_a = Trooper::factory()->create(['name' => 'Alpha', 'membership_status' => MembershipStatus::Pending]);

        // Act
        $results = Trooper::pendingApprovals()->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($pending_trooper_a));
        $this->assertTrue($results->contains($pending_trooper_b));
        $this->assertFalse($results->contains($active_trooper));

        // Assert order by name
        $this->assertEquals('Alpha', $results[0]->name);
        $this->assertEquals('Beta', $results[1]->name);
    }

    public function test_approvable_by_scope_returns_candidate_in_same_organization(): void
    {
        // Arrange
        $moderator = Trooper::factory()->create();
        $candidate = Trooper::factory()->asPending()->create();
        $organization = Organization::factory()->create();

        $moderator->trooper_assignments()->create([
            'organization_id' => $organization->id,
            'moderator' => true,
            'member' => true,
        ]);
        $candidate->trooper_assignments()->create(['organization_id' => $organization->id]);

        // Act
        $results = Trooper::moderatedBy($moderator)->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->first()->is($moderator));
        $this->assertTrue($results->skip(1)->first()->is($candidate));
    }

    public function test_approvable_by_scope_returns_candidate_in_child_organization(): void
    {
        // Arrange
        $moderator = Trooper::factory()->create();
        $candidate = Trooper::factory()->asPending()->create();
        $parent_org = Organization::factory()->create();
        $child_org = Organization::factory()->create(['parent_id' => $parent_org->id]);

        $moderator->trooper_assignments()->create([
            'organization_id' => $parent_org->id,
            'moderator' => true,
            'member' => false,
        ]);
        $candidate->trooper_assignments()->create(['organization_id' => $child_org->id]);

        // Act
        $results = Trooper::moderatedBy($moderator)->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->first()->is($moderator));
        $this->assertTrue($results->skip(1)->first()->is($candidate));
    }

    public function test_approvable_by_scope_excludes_candidate_in_unrelated_organization(): void
    {
        // Arrange
        $moderator = Trooper::factory()->create();
        $candidate = Trooper::factory()->asPending()->create();
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        $moderator->trooper_assignments()->create([
            'organization_id' => $org1->id,
            'member' => true,
            'moderator' => true,
        ]);
        $candidate->trooper_assignments()->create(['organization_id' => $org2->id]);

        // Act
        $results = Trooper::moderatedBy($moderator)->get();

        // Assert
        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($moderator));
    }

    public function test_approvable_by_scope_excludes_candidate_in_parent_organization(): void
    {
        // Arrange
        $moderator = Trooper::factory()->create();
        $candidate = Trooper::factory()->asPending()->create();
        $parent_org = Organization::factory()->create();
        $child_org = Organization::factory()->create(['parent_id' => $parent_org->id]);

        $moderator->trooper_assignments()->create([
            'organization_id' => $child_org->id,
            'member' => true,
            'moderator' => true,
        ]);
        $candidate->trooper_assignments()->create(['organization_id' => $parent_org->id]);

        // Act
        $results = Trooper::moderatedBy($moderator)->get();

        // Assert
        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($moderator));
    }
}
