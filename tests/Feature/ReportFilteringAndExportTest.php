<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class ReportFilteringAndExportTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function createUser(string $role = 'visualizador'): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_guests_are_redirected_to_login_on_all_report_routes(): void
    {
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/reports/export/excel')->assertRedirect('/login');
        $this->get('/reports/export/csv')->assertRedirect('/login');
        $this->get('/reports/export')->assertRedirect('/login');
        $this->get('/reports/print')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_departmental_report_with_date_filters(): void
    {
        $user = $this->createUser();
        $farm = $this->farm($user);
        $this->measurement($farm, '2026-08', 5000, 5200);

        $response = $this->actingAs($user)->get('/reports?type=departamental&start_date=2026-08-01&end_date=2026-08-31');

        $response->assertOk();
        $response->assertSee('Reporte Consolidado Departamental');
        $response->assertSee('Izabal');
        $response->assertSee('5,000'); // kWh
        $response->assertSee('2.00 Ton'); // 5000 * 0.40 / 1000 = 2.00 Ton
    }

    public function test_authenticated_user_can_view_farms_performance_report(): void
    {
        $user = $this->createUser();
        $farm = $this->farm($user);
        $this->measurement($farm, '2026-08', 4800, 5000);

        $response = $this->actingAs($user)->get('/reports?type=granjas');

        $response->assertOk();
        $response->assertSee('Reporte de Rendimiento por Granja Solar');
        $response->assertSee($farm->name);
        $response->assertSee('96.0%'); // 4800 / 5000 = 96%
    }

    public function test_authenticated_user_can_view_environmental_report(): void
    {
        $user = $this->createUser();
        $farm = $this->farm($user);
        $this->measurement($farm, '2026-08', 10000, 10000); // 10,000 * 0.40 = 4,000 kg CO2 = 4.00 Ton

        $response = $this->actingAs($user)->get('/reports?type=ambiental');

        $response->assertOk();
        $response->assertSee('Reporte Ambiental');
        $response->assertSee('4.00 Ton');
        $response->assertSee('Arboles');
    }

    public function test_authenticated_user_can_view_alerts_report(): void
    {
        $user = $this->createUser('operador');
        $farm = $this->farm($user);
        $gen = $this->measurement($farm, '2026-08', 75, 100); // 25% deficit

        GenerationAlert::create([
            'solar_farm_id' => $farm->id,
            'energy_generation_id' => $gen->id,
            'period' => '2026-08',
            'estimated_kwh' => 100,
            'real_kwh' => 75,
            'deviation_percentage' => 25.0,
            'status' => 'resolved',
            'resolution_notes' => 'Inversor principal reiniciado en campo.',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/reports?type=alertas');

        $response->assertOk();
        $response->assertSee('Reporte de Incidentes y Alertas Operativas');
        $response->assertSee('25.0%');
        $response->assertSee('Inversor principal reiniciado');
    }

    public function test_filter_by_department_limits_scope(): void
    {
        $user = $this->createUser();
        $farm1 = $this->farm($user); // Izabal

        $dept2 = Department::create(['code' => '050', 'name' => 'Escuintla', 'latitude' => 14.3, 'longitude' => -90.7]);
        $farm2 = SolarFarm::create([
            'department_id' => $dept2->id,
            'name' => 'Granja Costa Sur',
            'latitude' => 14.3,
            'longitude' => -90.7,
            'benefited_families' => 50,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/reports?type=granjas&department_id=' . $dept2->id);

        $response->assertOk();
        $response->assertSee('Granja Costa Sur');
        $response->assertDontSee($farm1->name);
    }

    public function test_excel_export_returns_valid_spreadsheet(): void
    {
        $user = $this->createUser();
        $farm = $this->farm($user);
        $this->measurement($farm, '2026-08', 2500, 2500);

        $response = $this->actingAs($user)->get('/reports/export/excel?type=departamental');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $this->assertStringContainsString("K'IN SOLAR GUATEMALA", $response->getContent());
        $this->assertStringContainsString('Reporte Consolidado Departamental', $response->getContent());
        $this->assertStringContainsString('Izabal', $response->getContent());
    }

    public function test_csv_export_returns_file_with_utf8_bom(): void
    {
        $user = $this->createUser();
        $farm = $this->farm($user);
        $this->measurement($farm, '2026-08', 2500, 2500);

        $response = $this->actingAs($user)->get('/reports/export/csv?type=departamental');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $content = $response->getContent();
        $this->assertTrue(str_starts_with($content, "\xEF\xBB\xBF"));
        $this->assertStringContainsString('Izabal', $content);
    }

    public function test_print_pdf_view_renders_executive_template(): void
    {
        $user = $this->createUser();
        $farm = $this->farm($user);
        $this->measurement($farm, '2026-08', 2500, 2500);

        $response = $this->actingAs($user)->get('/reports/print?type=departamental&auto_print=1');

        $response->assertOk();
        $response->assertSee("K'IN SOLAR GUATEMALA", false);
        $response->assertSee('Plantilla Ejecutiva Oficial para PDF');
        $response->assertSee('Supervisor de Operaciones Energéticas');
        $response->assertSee('window.print()', false);
    }

    public function test_invalid_date_range_fails_validation(): void
    {
        $user = $this->createUser();

        // end_date before start_date
        $response = $this->actingAs($user)->get('/reports?start_date=2026-09-10&end_date=2026-09-01');

        $response->assertSessionHasErrors(['end_date']);
    }
}
