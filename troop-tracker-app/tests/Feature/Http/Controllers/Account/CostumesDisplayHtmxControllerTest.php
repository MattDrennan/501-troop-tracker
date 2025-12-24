<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Account;

use App\Models\Costume;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Account\TrooperCostumesDisplayHtmxController
 */
class CostumesDisplayHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    private Trooper $trooper;
    private Organization $active_organization;
    private Costume $trooper_costume;

    protected function setUp(): void
    {
        parent::setUp();

        $this->active_organization = Organization::factory()->withCostume('Stormtrooper')->create();
        $this->trooper_costume = $this->active_organization->costumes()->first();

        $inactive_organization = Organization::factory()->create();

        $this->trooper = Trooper::factory()
            ->withAssignment($this->active_organization, member: true)
            ->withAssignment($inactive_organization, member: false)
            ->withCostume($this->trooper_costume)
            ->create();
    }

    public function test_invoke_without_organization_id_returns_correct_view_and_data(): void
    {
        // Arrange
        $this->actingAs($this->trooper);

        // Act
        $response = $this->get(route('account.costumes-htmx'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.costumes');

        $response->assertViewHas('organizations', function (Collection $organizations)
        {
            return $organizations->count() === 1
                && $organizations->first()->is($this->active_organization);
        });

        $response->assertViewHas('selected_organization', null);
        $response->assertViewHas('costumes', []);
        $response->assertViewHas('trooper_costumes', function (Collection $trooper_costumes)
        {
            return $trooper_costumes->count() === 1
                && $trooper_costumes->first()->is($this->trooper_costume);
        });
    }

    public function test_invoke_with_organization_id_returns_correct_view_and_data(): void
    {
        // Arrange
        $this->actingAs($this->trooper);
        $new_costume = Costume::factory()->create(['organization_id' => $this->active_organization->id]);

        // Act
        $response = $this->get(route('account.costumes-htmx', [
            'organization_id' => $this->active_organization->id
        ]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.costumes');

        $response->assertViewHas('organizations', function (Collection $organizations)
        {
            return $organizations->count() === 1
                && $organizations->first()->is($this->active_organization);
        });

        $response->assertViewHas('selected_organization', function (Organization $selected_organization)
        {
            return $selected_organization->is($this->active_organization);
        });

        $response->assertViewHas('costumes', function (array $costumes) use ($new_costume)
        {
            return count($costumes) === 1
                && array_key_exists($new_costume->id, $costumes);
        });

        $response->assertViewHas('trooper_costumes', function (Collection $trooper_costumes)
        {
            return $trooper_costumes->count() === 1
                && $trooper_costumes->first()->is($this->trooper_costume);
        });
    }

    public function test_invoke_with_organization_id_excludes_already_assigned_costumes(): void
    {
        // Arrange
        $this->actingAs($this->trooper);

        // Act
        $response = $this->get(route('account.costumes-htmx', [
            'organization_id' => $this->active_organization->id
        ]));

        // Assert
        $response->assertOk();
        $response->assertViewHas('costumes', function (array $costumes)
        {
            // The trooper already has the only costume for this organization, so the list should be empty.
            return empty($costumes);
        });
    }

    public function test_invoke_does_not_show_costumes_for_unassigned_organization(): void
    {
        // Arrange
        $this->actingAs($this->trooper);
        $unassigned_organization = Organization::factory()->create();

        // Act
        $response = $this->get(route('account.costumes-htmx', [
            'organization_id' => $unassigned_organization->id
        ]));

        // Assert
        $response->assertOk();
        $response->assertViewHas('selected_organization', null);
        $response->assertViewHas('costumes', []);
    }
}
