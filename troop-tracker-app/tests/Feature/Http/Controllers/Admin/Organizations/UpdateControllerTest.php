<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Organizations;

use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class UpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_view_for_authorized_trooper(): void
    {
        // Arrange
        $trooper = Trooper::factory()->asAdmin()->create();
        $this->actingAs($trooper);

        $organization = Organization::factory()->create();

        // Act
        $response = $this->get(route('admin.organizations.update', ['organization' => $organization]));

        // Assert
        $response->assertOk();
        $response->assertViewIs('pages.admin.organizations.update');
        $response->assertViewHas('organization', $organization);
    }

    public function test_invoke_returns_forbidden_for_unauthorized_trooper(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create();
        $this->actingAs($trooper);

        $organization = Organization::factory()->create();

        // Act
        $response = $this->get(route('admin.organizations.update', ['organization' => $organization]));

        // Assert
        $response->assertForbidden();
    }
}