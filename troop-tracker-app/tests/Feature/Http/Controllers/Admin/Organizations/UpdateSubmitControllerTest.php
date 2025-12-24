<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Organizations;

use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateSubmitControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_updates_organization_and_redirects(): void
    {
        // Arrange
        $trooper = Trooper::factory()->asAdmin()->create();
        $this->actingAs($trooper);

        $organization = Organization::factory()->unit()->create();

        $new_name = 'New Updated Name';

        // Act
        $response = $this->post(
            route('admin.organizations.update', ['organization' => $organization]),
            ['name' => $new_name]
        );

        // Assert
        $response->assertRedirect(route('admin.organizations.list'));

        $this->assertDatabaseHas(Organization::class, [
            'id' => $organization->id,
            'name' => $new_name,
        ]);
    }
}