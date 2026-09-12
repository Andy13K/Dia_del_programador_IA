<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\McpStoreGenerationRequest;
use App\Models\SolarFarm;
use App\Models\User;
use App\Services\BackendAuditService;
use App\Services\EnergyGenerationService;
use Illuminate\Http\JsonResponse;

class McpGenerationController extends Controller
{
    public function __construct(
        private EnergyGenerationService $generations,
        private BackendAuditService $audit,
    ) {}

    public function store(McpStoreGenerationRequest $request): JsonResponse
    {
        $farm = SolarFarm::query()->findOrFail($request->integer('solar_farm_id'));
        $systemUserId = User::query()->where('email', 'mcp-agent@kinsolar.internal')->value('id');

        // OWASP A10: fallar cerrado si el usuario sistema no existe (seeder no corrido).
        if ($systemUserId === null) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario sistema del MCP no configurado. Ejecutar el seeder correspondiente.',
            ], 500);
        }

        $generation = $this->generations->store($farm, $request->validated(), $systemUserId);
        $this->audit->record($request, 'created', $generation, $systemUserId);
        if ($generation->generationAlert !== null) {
            $this->audit->record($request, 'created', $generation->generationAlert, $systemUserId);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $generation->id,
                'solar_farm_id' => $generation->solar_farm_id,
                'period' => $generation->period,
                'estimated_kwh' => (float) $generation->estimated_kwh,
                'real_kwh' => (float) $generation->real_kwh,
                'co2_kg' => (float) $generation->co2_kg,
                'alert_created' => $generation->generationAlert !== null,
                'deviation_percentage' => $generation->generationAlert?->deviation_percentage,
            ],
        ], 201);
    }
}
