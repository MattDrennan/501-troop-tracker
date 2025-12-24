<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin\Settings;

use App\Models\Setting;
use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_guest_is_redirected_to_login(): void
    {
        // Arrange
        $setting = Setting::factory()->create();

        // Act
        $response = $this->post(route('admin.settings.update-htmx', $setting));

        // Assert
        $response->assertRedirect(route('auth.login'));
    }

    public function test_invoke_unauthorized_user_is_forbidden(): void
    {
        // Arrange
        $unauthorized_user = Trooper::factory()->create();
        $setting = Setting::factory()->create();

        // Act
        $response = $this->actingAs($unauthorized_user)
            ->post(route('admin.settings.update-htmx', $setting));

        // Assert
        $response->assertForbidden();
    }

    public function test_invoke_authorized_user_can_update_setting(): void
    {
        // Arrange
        $admin = Trooper::factory()->asAdmin()->create();
        $setting = Setting::factory()->create(['value' => 'old_value']);
        $new_value = 'new_value';

        $update_data = [
            'setting' => [
                $setting->key => $new_value,
            ],
        ];

        $expected_message = json_encode([
            'message' => "Setting {$setting->key} successfully saved.",
            'type' => 'success',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.settings.update-htmx', $setting), $update_data);

        // Assert
        $response->assertOk();
        $response->assertContent('ok');
        $response->assertHeader('X-Flash-Message', $expected_message);

        $this->assertDatabaseHas(Setting::class, [
            'key' => $setting->key,
            'value' => $new_value,
        ]);
    }
}