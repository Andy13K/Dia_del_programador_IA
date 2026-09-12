<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelOptionsEscapingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comilla invertida (rompe un template literal de JS), ${...} (interpolación de JS) y
     * una etiqueta <script> (HTML): las tres formas de romper el render anterior, que
     * interpolaba {{ $panel->brand }} directamente dentro de un string de JS.
     */
    private const PAYLOAD = 'Evil`);alert(document.cookie);//${1+1}<script>alert(2)</script>';

    public function test_malicious_panel_brand_is_not_emitted_unescaped_in_farms_create(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->seedDepartmentAndMaliciousPanel();

        $html = $this->actingAs($admin)->get(route('farms.create'))->getContent();

        $this->assertPayloadIsNeutralized($html);
    }

    public function test_malicious_panel_brand_is_not_emitted_unescaped_in_farms_edit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $department = $this->seedDepartmentAndMaliciousPanel();
        $farm = SolarFarm::query()->create([
            'department_id' => $department->id,
            'name' => 'Granja de prueba',
            'latitude' => 15.7,
            'longitude' => -88.6,
            'benefited_families' => 10,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $html = $this->actingAs($admin)->get(route('farms.edit', $farm))->getContent();

        $this->assertPayloadIsNeutralized($html);
    }

    private function seedDepartmentAndMaliciousPanel(): Department
    {
        $department = Department::query()->create([
            'name' => 'Izabal', 'code' => '180', 'latitude' => 15.7, 'longitude' => -88.6,
        ]);

        SolarPanel::query()->create([
            'brand' => self::PAYLOAD, 'model' => 'X', 'nominal_power_kw' => 0.5, 'status' => 'active',
        ]);

        return $department;
    }

    private function assertPayloadIsNeutralized(string $html): void
    {
        // La carga cruda nunca debe aparecer tal cual: eso significaría que rompió el
        // contexto (JS o HTML) en el que se insertó.
        $this->assertStringNotContainsString(self::PAYLOAD, $html);
        $this->assertStringNotContainsString('<script>alert(2)</script>', $html);

        // El texto sí debe llegar al navegador, solo que codificado de forma segura
        // (@js() usa JSON con secuencias \uXXXX para los caracteres peligrosos).
        $this->assertStringContainsString('Evil', $html);
    }
}
