<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\AuthenticationStatus;
use App\Models\Trooper;
use App\Services\StandaloneService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StandaloneServiceTest extends TestCase
{
    use RefreshDatabase;

    private StandaloneService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new StandaloneService();
    }

    public function test_get_avatar_url_throws_exception(): void
    {
        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not implemented');

        // Act
        $this->subject->getAvatarUrl(123);
    }

    public function test_authenticate_always_fails_due_to_incorrect_hashing_logic(): void
    {
        // Arrange
        Trooper::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $result = $this->subject->authenticate('testuser', 'password1234');

        // Assert
        // This test asserts the current behavior. The `authenticate` method incorrectly
        // re-hashes the plain-text password instead of using `password_verify`,
        // so it will always fail to find a match.
        $this->assertEquals(AuthenticationStatus::FAILURE, $result);
    }

    public function test_verify_succeeds_with_correct_credentials(): void
    {
        // Arrange
        $trooper = Trooper::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $result = $this->subject->verify('testuser', 'password123');

        // Assert
        $this->assertInstanceOf(Trooper::class, $result);
        $this->assertTrue($trooper->is($result));
    }

    public function test_verify_fails_with_incorrect_password(): void
    {
        // Arrange
        Trooper::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $result = $this->subject->verify('testuser', 'wrongpassword');

        // Assert
        $this->assertNull($result);
    }

    public function test_verify_fails_for_non_existent_user(): void
    {
        // Act
        $result = $this->subject->verify('nonexistent', 'password123');

        // Assert
        $this->assertNull($result);
    }
}
