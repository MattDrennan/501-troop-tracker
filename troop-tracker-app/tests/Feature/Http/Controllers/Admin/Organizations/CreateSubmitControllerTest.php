<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Organizations;

use App\Enums\OrganizationType;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSubmitControllerTest extends TestCase
{
    use RefreshDatabase;

    private Trooper $admin_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin_user = Trooper::factory()->asAdmin()->create(); // Assuming a basic user can perform this
    }

    public function test_invoke_creates_region_under_organization_and_redirects(): void
    {
        // Arrange
        $parent_organization = Organization::factory()->create([
            'type' => OrganizationType::Organization,
        ]);

        $new_org_name = 'New Test Region';

        // Act
        $response = $this->actingAs($this->admin_user)
            ->post(route('admin.organizations.create', ['parent' => $parent_organization]), [
                'name' => $new_org_name,
            ]);

        // Assert
        $response->assertRedirect(route('admin.organizations.list'));

        $this->assertDatabaseHas(Organization::class, [
            'name' => $new_org_name,
            'parent_id' => $parent_organization->id,
            'type' => OrganizationType::Region->value,
        ]);
    }

    public function test_invoke_creates_unit_under_region_and_redirects(): void
    {
        // Arrange
        $parent_organization = Organization::factory()->create([
            'type' => OrganizationType::Region,
        ]);

        $new_org_name = 'New Test Unit';

        // Act
        $response = $this->actingAs($this->admin_user)
            ->post(route('admin.organizations.create', ['parent' => $parent_organization]), [
                'name' => $new_org_name,
            ]);

        // Assert
        $response->assertRedirect(route('admin.organizations.list'));

        $this->assertDatabaseHas(Organization::class, [
            'name' => $new_org_name,
            'parent_id' => $parent_organization->id,
            'type' => OrganizationType::Unit->value,
        ]);
    }

    public function test_invoke_throws_exception_for_invalid_parent_type(): void
    {
        // Arrange
        $parent_organization = Organization::factory()->unit()->create();

        $new_org_name = 'This Should Fail';

        // Act

        $response = $this->actingAs($this->admin_user)
            ->post(route('admin.organizations.create', ['parent' => $parent_organization]), [
                'name' => $new_org_name,
            ]);

        //  Assert
        $response->assertStatus(500);
    }
}