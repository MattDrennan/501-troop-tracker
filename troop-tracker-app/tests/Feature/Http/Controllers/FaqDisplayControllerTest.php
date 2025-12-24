<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class FaqDisplayControllerTest extends TestCase
{
    public function test_displays_the_faq_page()
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertViewIs('pages.faq');
        // $response->assertSee('Frequently Asked Questions'); // optional content check
    }
}