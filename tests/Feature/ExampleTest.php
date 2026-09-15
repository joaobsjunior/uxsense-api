<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_landing_page_is_served(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('REST API');
    }

    public function test_the_api_root_answers(): void
    {
        $this->getJson('/api')->assertStatus(200)->assertJson(['API SERVER']);
    }

    public function test_phpinfo_is_no_longer_exposed(): void
    {
        $this->get('/info')->assertStatus(404);
    }

    public function test_cors_headers_are_emitted_for_api_requests(): void
    {
        $response = $this->getJson('/api', ['Origin' => 'https://www.uxsense.com.br']);

        $response->assertStatus(200);
        $response->assertHeader('Access-Control-Allow-Origin', '*');
    }
}
