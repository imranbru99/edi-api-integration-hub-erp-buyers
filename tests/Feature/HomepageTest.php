<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('EDI', false);
    }
}
