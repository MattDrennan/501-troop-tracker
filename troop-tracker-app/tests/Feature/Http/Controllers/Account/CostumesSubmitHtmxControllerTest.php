<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Account;

use App\Models\Costume;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperCostume;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CostumesSubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    private Trooper $trooper;
    private Organization $assigned_organization;
    private Costume $assigned_costume;
    private Organization $unassigned_organization;
    private Costume $unassigned_costume;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assigned_organization = Organization::factory()->withCostume('Stormtrooper')->create();
        $this->assigned_costume = $this->assigned_organization->costumes()->first();

        $this->unassigned_organization = Organization::factory()->withCostume('Stormtrooper I')->create();
        $this->unassigned_costume = $this->unassigned_organization->costumes()->first();

        $this->trooper = Trooper::factory()
            ->withOrganization($this->assigned_organization, 'TK-1')
            ->withCostume($this->assigned_costume)
            ->withAssignment($this->assigned_organization, notify: true, member: true)
            ->create();
    }

    public function test_invoke_adds_trooper_costume_for_valid_request(): void
    {
        // Arrange
        $this->trooper->detachCostume($this->assigned_costume->id);

        $this->assertDatabaseMissing('tt_trooper_costumes', [
            'trooper_id' => $this->trooper->id,
            'costume_id' => $this->assigned_costume->id,
        ]);

        $request_data = [
            'organization_id' => $this->assigned_organization->id,
            'costume_id' => $this->assigned_costume->id,
        ];

        // Act
        $response = $this->actingAs($this->trooper)
            ->post(route('account.costumes-htmx'), $request_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.costumes');
        $response->assertViewHas('trooper_costumes', function (Collection $trooper_costumes): bool
        {
            return $trooper_costumes->count() === 1
                && $trooper_costumes->first()->id === $this->assigned_costume->id;
        });

        $this->assertDatabaseHas(TrooperCostume::class, [
            'trooper_id' => $this->trooper->id,
            'costume_id' => $this->assigned_costume->id,
        ]);
    }

    public function test_invoke_does_not_add_trooper_for_unassigned_club(): void
    {
        // Arrange
        $request_data = [
            'organization_id' => $this->unassigned_organization->id,
            'costume_id' => $this->unassigned_costume->id,
        ];

        // Act
        $response = $this->actingAs($this->trooper)
            ->post(route('account.costumes-htmx'), $request_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.costumes');
        $response->assertViewHas('trooper_costumes', function (Collection $trooper_costumes): bool
        {
            return $trooper_costumes->count() == 1;
        });

        $this->assertDatabaseMissing('tt_trooper_costumes', [
            'trooper_id' => $this->trooper->id,
            'costume_id' => $this->unassigned_costume->id,
        ]);
    }
}
