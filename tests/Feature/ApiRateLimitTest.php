<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_v1_allows_normal_use_under_the_limit(): void
    {
        $response = $this->getJson('/api/v1/statistics');

        $response->assertOk();
        $response->assertHeader('X-RateLimit-Limit', '60');
    }

    public function test_api_v1_enforces_a_request_limit_per_ip(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/statistics')->assertOk();
        }

        $this->getJson('/api/v1/statistics')->assertStatus(429);
    }
}
