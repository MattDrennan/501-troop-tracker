<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Enums\MembershipRole;
use App\Models\Setting;
use App\Models\Trooper;
use App\Policies\SettingPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SettingPolicyTest extends TestCase
{
    use RefreshDatabase;

    private Setting $setting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setting = Setting::factory()->create();
    }

    #[DataProvider('permissionProvider')]
    public function test_admin_can_perform_actions(string $method, bool $expected_result): void
    {
        // Arrange
        $admin = Trooper::factory()->create(['membership_role' => MembershipRole::Administrator]);
        $subject = new SettingPolicy();

        // Act & Assert
        $this->assertEquals($expected_result, $subject->$method($admin, $this->setting));
    }

    #[DataProvider('permissionProvider')]
    public function test_non_admin_cannot_perform_actions(string $method, $expected_result): void
    {
        // Arrange
        $moderator = Trooper::factory()->create(['membership_role' => MembershipRole::Moderator]);
        $regular_user = Trooper::factory()->create(['membership_role' => MembershipRole::Member]);
        $subject = new SettingPolicy();

        // Act & Assert
        $this->assertFalse($subject->$method($moderator, $this->setting));
        $this->assertFalse($subject->$method($regular_user, $this->setting));
    }

    /**
     * Provides policy methods and their expected outcomes for an admin user.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function permissionProvider(): array
    {
        return [
            'viewAny' => [
                'method' => 'viewAny',
                'expected_result' => true,
            ],
            'view' => [
                'method' => 'view',
                'expected_result' => true,
            ],
            'create' => [
                'method' => 'create',
                'expected_result' => true,
            ],
            'update' => [
                'method' => 'update',
                'expected_result' => true,
            ],
            'delete' => [
                'method' => 'delete',
                'expected_result' => false,
            ],
            'restore' => [
                'method' => 'restore',
                'expected_result' => false,
            ],
            'forceDelete' => [
                'method' => 'forceDelete',
                'expected_result' => false,
            ],
        ];
    }
}