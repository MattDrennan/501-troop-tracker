<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use Database\Seeders\OrganizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class RegisterDisplayControllerTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface $club_service_mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(OrganizationSeeder::class);
    }

    public function test_invoke_returns_register_view_with_clubs_data(): void
    {
        // Act: Make a GET request to the registration route
        $response = $this->get(route('auth.register'));

        // Assert: Verify the response and view data
        $response->assertOk();
        $response->assertViewIs('pages.auth.register');
        $response->assertViewHas('organizations');
    }
}