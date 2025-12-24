<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Auth;

use App\Contracts\AuthenticationInterface;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use App\Models\TrooperOrganization;
use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Auth\RegisterSubmitController
 */
class RegisterSubmitControllerTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface $auth_mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth_mock = $this->mock(AuthenticationInterface::class);
    }

    public function test_invoke_with_invalid_credentials_fails_registration_with_xenforo(): void
    {
        // Arrange
        Config::set('tracker.plugins.type', 'xenforo');

        $this->auth_mock->shouldReceive('verify')
            ->once()
            ->with('testuser', 'password')
            ->andReturn(null);

        $request_data = [
            'username' => 'testuser',
            'password' => 'password',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account_type' => 'member',
        ];

        // Act
        $response = $this->post(route('auth.register'), $request_data);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHasErrors(['username' => 'Invalid Credentials']);
        $this->assertDatabaseMissing(Trooper::class, ['username' => 'testuser']);
    }

    public function test_invoke_with_valid_credentials_registers_member_successfully_with_xenforo(): void
    {
        // Arrange
        Config::set('tracker.plugins.type', 'xenforo');

        $organization = Organization::factory()->create(['identifier_validation' => 'string']);

        $unit = Organization::factory()->unit()->create();
        $region = $unit->parent;
        $organization = $region->parent;

        $organization->identifier_validation = 'string';
        $organization->save();

        $this->auth_mock->shouldReceive('verify')
            ->once()
            ->with('testuser', 'password')
            ->andReturn('auth123');

        $request_data = [
            'username' => 'testuser',
            'password' => 'password',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account_type' => 'member',
            'organizations' => [
                $organization->id => [
                    'selected' => '1',
                    'identifier' => 'TK12345',
                    'region_id' => $region->id,
                    'unit_id' => $unit->id,
                ],
            ],
        ];

        // Act
        $response = $this->post(route('auth.register'), $request_data);

        // Assert
        $response->assertRedirect(route('auth.register'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas(Trooper::class, [
            'username' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $trooper = Trooper::where('username', 'testuser')->first();

        $this->assertDatabaseHas(TrooperOrganization::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $organization->id,
            'identifier' => 'TK12345',
        ]);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $organization->id,
            'notify' => true,
            'member' => false,
        ]);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $region->id,
            'notify' => true,
            'member' => false,
        ]);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $unit->id,
            'notify' => true,
            'member' => true,
        ]);
    }

    public function test_invoke_registers_handler_successfully_with_xenforo(): void
    {
        // Arrange
        Config::set('tracker.plugins.type', 'xenforo');

        $organization = Organization::factory()->create();

        $this->auth_mock->shouldReceive('verify')
            ->once()
            ->with('handleruser', 'password')
            ->andReturn('auth123');

        $request_data = [
            'username' => 'handleruser',
            'password' => 'password',
            'name' => 'Handler User',
            'email' => 'handler@example.com',
            'account_type' => 'handler',
            'organizations' => [
                $organization->id => [
                    'selected' => '1',
                    'identifier' => 'TK-ID',
                ],
            ],
        ];

        // Act
        $response = $this->post(route('auth.register'), $request_data);

        // Assert
        $response->assertRedirect(route('auth.register'));
        $trooper = Trooper::where('username', 'handleruser')->first();

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $organization->id,
            'member' => true
        ]);
    }

    public function test_invoke_with_valid_credentials_registers_member_successfully_with_standalone(): void
    {
        // Arrange
        Config::set('tracker.plugins.type', 'standalone');

        $organization = Organization::factory()->create(['identifier_validation' => 'string']);
        $region = Organization::factory()->region()->create(['parent_id' => $organization->id]);
        $unit = Organization::factory()->unit()->create(['parent_id' => $region->id]);

        $this->auth_mock->shouldNotReceive('verify');

        $request_data = [
            'username' => 'testuser',
            'password' => 'password',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account_type' => 'member',
            'organizations' => [
                $organization->id => [
                    'selected' => '1',
                    'identifier' => 'TK12345',
                    'region_id' => $region->id,
                    'unit_id' => $unit->id,
                ],
            ],
        ];

        // Act
        $response = $this->post(route('auth.register'), $request_data);

        // Assert
        $response->assertRedirect(route('auth.register'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas(Trooper::class, [
            'username' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $trooper = Trooper::where('username', 'testuser')->first();

        $this->assertDatabaseHas(TrooperOrganization::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $organization->id,
            'identifier' => 'TK12345',
        ]);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $organization->id,
            'notify' => true,
            'member' => false,
        ]);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $region->id,
            'notify' => true,
            'member' => false,
        ]);

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $unit->id,
            'notify' => true,
            'member' => true,
        ]);
    }

    public function test_invoke_registers_handler_successfully_with_standalone(): void
    {
        // Arrange
        Config::set('tracker.plugins.type', 'standalone');

        $organization = Organization::factory()->create();

        $this->auth_mock->shouldNotReceive('verify');

        $request_data = [
            'username' => 'handleruser',
            'password' => 'password',
            'name' => 'Handler User',
            'email' => 'handler@example.com',
            'account_type' => 'handler',
            'organizations' => [
                $organization->id => [
                    'selected' => '1',
                    'identifier' => 'TK-ID',
                ],
            ],
        ];

        // Act
        $response = $this->post(route('auth.register'), $request_data);

        // Assert
        $response->assertRedirect(route('auth.register'));
        $trooper = Trooper::where('username', 'handleruser')->first();

        $this->assertDatabaseHas(TrooperAssignment::class, [
            'trooper_id' => $trooper->id,
            'organization_id' => $organization->id,
            'member' => true,
        ]);
    }
}