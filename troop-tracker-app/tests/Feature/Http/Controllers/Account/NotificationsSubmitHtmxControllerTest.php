<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Account;

use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsSubmitHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization1;
    private Organization $organization2;
    private Organization $region1;
    private Organization $region2;
    private Organization $unit1;
    private Organization $unit2;
    private Organization $unit3;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->unit1 = Organization::factory()->unit()->create();
        $this->unit2 = Organization::factory()->unit()->create();
        $this->unit3 = Organization::factory()->unit()->create();

        $this->region1 = $this->unit1->parent;
        $this->region2 = $this->unit2->parent;

        $this->organization1 = $this->region1->parent;
        $this->organization2 = $this->region2->parent;
    }

    public function test_invoke_updates_notifications_and_returns_view_with_flash_message(): void
    {
        // Arrange
        $trooper = Trooper::factory()
            ->withOrganization($this->organization1, 'TK-1')
            ->withOrganization($this->organization2, 'BH-1')
            ->withAssignment($this->region1, notify: true)
            ->withAssignment($this->region2, notify: true)
            ->withAssignment($this->unit1, notify: true)
            ->withAssignment($this->unit2, notify: true)
            ->withAssignment($this->unit3, notify: true)
            ->create([
                'instant_notification' => 0,
                'attendance_notification' => 0,
                'command_staff_notification' => 0,
            ]);

        $request_data = [
            'instant_notification' => '1',
            'attendance_notification' => '1',
            'command_staff_notification' => '1',
            'organizations' => [
                $this->organization1->id => ['notification' => '1'], // Select Org 1
                // Org 2 is not sent, so it should be deselected
            ],
            'regions' => [
                $this->region1->id => ['notification' => '1'], // Select Region 1
                // Region 2 is not sent, so it should be deselected
            ],
            'units' => [
                $this->unit1->id => ['notification' => '1'], // Select Unit 1
                // Unit 2 & 3 are not sent, so they should be deselected
            ],
        ];

        // Act
        $response = $this->actingAs($trooper)->post(route('account.notifications-htmx'), $request_data);

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.account.notifications');
        $response->assertViewHas('organizations');

        $expected_flash_message = json_encode([
            'message' => 'Notifications updated successfully!',
            'type' => 'success',
        ]);
        $response->assertHeader('X-Flash-Message', $expected_flash_message);

        // Assert trooper base notifications were updated in the database
        $trooper->refresh();
        $this->assertEquals(1, $trooper->instant_notification);
        $this->assertEquals(1, $trooper->attendance_notification);
        $this->assertEquals(1, $trooper->command_staff_notification);

        // Assert pivot tables were updated in the database
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->organization1->id, 'notify' => 1]);
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->organization2->id, 'notify' => 0]);
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->region1->id, 'notify' => 1]);
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->region2->id, 'notify' => 0]);
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->unit1->id, 'notify' => 1]);
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->unit2->id, 'notify' => 0]);
        $this->assertDatabaseHas(TrooperAssignment::class, ['trooper_id' => $trooper->id, 'organization_id' => $this->unit3->id, 'notify' => 0]);

        // Assert the data returned to the view is correct
        $view_data = $response->getOriginalContent()->getData();
        $this->assertEquals(1, $view_data['instant_notification']);
        $this->assertEquals(1, $view_data['attendance_notification']);
        $this->assertEquals(1, $view_data['command_staff_notification']);

        $view_orgs = $view_data['organizations'];
        $view_org1 = $view_orgs->first();
        $view_org2 = $view_orgs->firstWhere('id', $this->organization2->id);
        $view_region1 = $view_org1->organizations->firstWhere('id', $this->region1->id);
        $view_region2 = $view_org2->organizations->firstWhere('id', $this->region2->id);
        $view_unit1 = $view_region1->organizations->firstWhere('id', $this->unit1->id);

        $this->assertTrue($view_org1->selected);
        $this->assertFalse($view_org2->selected);
        $this->assertTrue($view_region1->selected);
        $this->assertFalse($view_region2->selected);
        $this->assertTrue($view_unit1->selected);
    }
}