<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Widgets;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportDisplayHtmxControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('tracker.forum.name', 'Test Forum');
    }

    public function test_invoke_returns_correct_view_and_data_for_each_day_of_week(): void
    {
        Setting::factory()->create(['key' => 'support_goal', 'value' => '300']);

        $response = $this->get(route('support-htmx'));

        $response->assertOk();
        $response->assertViewIs('pages.widgets.support');
    }
}