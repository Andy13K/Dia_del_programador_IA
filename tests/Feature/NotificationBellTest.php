<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\GenerationAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function operator(): User
    {
        $user = User::factory()->create();
        $user->role = 'operador';
        $user->save();

        return $user;
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $response = $this->getJson('/alerts/notifications');
        $response->assertUnauthorized();
    }

    public function test_authenticated_user_receives_active_alerts(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        // Crear alerta activa
        $alert = GenerationAlert::create([
            'solar_farm_id' => $farm->id,
            'period' => '2026-09',
            'estimated_kwh' => 1000.0,
            'real_kwh' => 700.0,
            'deviation_percentage' => 30.0,
            'status' => 'active',
            'notes' => 'Alerta de prueba SCADA',
        ]);

        $response = $this->actingAs($user)->getJson('/alerts/notifications');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'count' => 1,
            ])
            ->assertJsonStructure([
                'success',
                'count',
                'alerts' => [
                    '*' => [
                        'id',
                        'farm_name',
                        'department_name',
                        'period',
                        'deviation_percentage',
                        'estimated_kwh',
                        'real_kwh',
                        'notes',
                        'created_at_human',
                        'show_url',
                    ]
                ]
            ]);
    }

    public function test_resolved_alerts_are_excluded_from_notifications(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        GenerationAlert::create([
            'solar_farm_id' => $farm->id,
            'period' => '2026-09',
            'estimated_kwh' => 1000.0,
            'real_kwh' => 700.0,
            'deviation_percentage' => 30.0,
            'status' => 'resolved',
            'notes' => 'Alerta resuelta',
        ]);

        $response = $this->actingAs($user)->getJson('/alerts/notifications');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'count' => 0,
                'alerts' => [],
            ]);
    }

    public function test_topbar_renders_notification_bell(): void
    {
        $user = $this->operator();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('btnNotificationBell')
            ->assertSee('notificationBadge')
            ->assertSee('notificationDropdown');
    }
}
