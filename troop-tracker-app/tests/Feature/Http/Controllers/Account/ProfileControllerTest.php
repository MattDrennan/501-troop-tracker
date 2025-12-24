<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Account;

use App\Models\Trooper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('account.display'));

        $response->assertRedirect(route('auth.login'));
    }

    public function test_authenticated_user_can_access_account_page(): void
    {
        $trooper = Trooper::factory()->create();

        $response = $this->actingAs($trooper)->get(route('account.display'));

        $response->assertOk();
        $response->assertViewIs('pages.account.display');
    }
}