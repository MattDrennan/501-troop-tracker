<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Organization;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(OrganizationSeeder::class);
    }

    public function test_invoke_returns_partial_view_with_club_selected(): void
    {
        // Arrange: Create a organization and define the input data for selection.
        $organization = Organization::find(1);
        $input_data = [
            'organizations' => [
                $organization->id => ['selected' => '1'],
            ],
        ];

        // Act: Simulate an HTMX POST request to the controller's route.
        // We assume a route is defined, e.g., 'auth.register-htmx'
        $response = $this->withHeaders(['HX-Request' => 'true'])
            ->post(route('auth.register-htmx', ['organization' => $organization->id]), $input_data);

        // Assert: Check for a successful response and the correct view.
        $response->assertOk();
        $response->assertViewIs('pages.auth.organization-selection');

        // Assert that the 'organization' object passed to the view has 'selected' set to true.
        $response->assertViewHas('organization', function ($view_club)
        {
            return $view_club->selected === true;
        });
    }

    public function test_invoke_returns_partial_view_with_club_not_selected(): void
    {
        // Arrange: Create a organization and define input data where the organization is not selected.
        $organization = Organization::find(1);
        $input_data = [
            'organizations' => [
                $organization->id => ['selected' => '0'], // or null/not present
            ],
        ];

        // Act: Simulate an HTMX POST request.
        $response = $this->withHeaders(['HX-Request' => 'true'])
            ->post(route('auth.register-htmx', ['organization' => $organization->id]), $input_data);

        // Assert: Check for a successful response and the correct view.
        $response->assertOk();
        $response->assertViewIs('pages.auth.organization-selection');

        // Assert that the 'organization' object passed to the view has 'selected' set to false.
        $response->assertViewHas('organization', function ($view_club)
        {
            return $view_club->selected === false;
        });
    }
}