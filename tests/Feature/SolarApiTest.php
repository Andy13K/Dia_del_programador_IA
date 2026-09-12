<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class SolarApiTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    public function test_public_endpoints_return_the_success_envelope(): void
    {
        $farm = $this->farm();
        $this->measurement($farm, '2025-01', 100);
        foreach (['departments', 'farms', 'statistics'] as $endpoint) {
            $response = $this->getJson('/api/v1/'.$endpoint);
            $response->assertOk()->assertJsonPath('success', true)->assertJsonStructure(['success', 'data']);
            $this->assertIsArray($response->json('data'));
        }
        $this->getJson('/api/v1/departments')->assertJsonPath('data.0.name', 'Izabal');
        $this->getJson('/api/v1/farms')->assertJsonPath('data.0.id', $farm->id);
        $this->getJson('/api/v1/statistics')->assertJsonPath('data.total_farms', 1)->assertJsonPath('data.total_kwh', 100)->assertJsonPath('data.total_co2_kg', 40);
    }
}
