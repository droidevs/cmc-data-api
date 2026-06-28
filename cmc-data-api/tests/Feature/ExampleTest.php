<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verify the dashboard route responds successfully.
     * Uses RefreshDatabase so the in-memory SQLite schema exists.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
    }
}
