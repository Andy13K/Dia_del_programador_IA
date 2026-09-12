<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Department;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use App\Models\User;

class ReportService
{
    /**
     * Factor normativo oficial de mitigacion de CO2 en Guatemala (CNEE).
     * 0.40 kg CO2 evitado por cada kWh generado.
     */
    public const CO2_FACTOR = 0.40;

    /**
     * Obtiene los datos consolidados y estructurados para el reporte segun filtros.
     *
     * @return array<string, mixed>
     */
    public function getReportData(
        string $type = 'departamental',
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $departmentId = null,
        ?User $user = null
    ): array {
        $allowedTypes = ['departamental', 'granjas', 'ambiental', 'alertas'];
        if (!in_array($type, $allowedTypes, true)) {
            $type = 'departamental';
        }

        $dept = $departmentId ? Department::find($departmentId) : null;
        $deptName = $dept ? $dept->name : 'Todos los Departamentos (Nacional)';

        $baseInfo = [
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'department_id' => $departmentId,
            'department_name' => $deptName,
            'generated_at' => now()->timezone('America/Guatemala')->format('d/m/Y H:i:s'),
            'generated_by' => $user ? $user->name : "Sistema K'in Solar",
            'user_role' => $user ? ucfirst((string) $user->role) : 'Visualizador',
            'co2_factor' => self::CO2_FACTOR,
        ];

        return match ($type) {
            'granjas' => $this->buildFarmsReport($baseInfo, $startDate, $endDate, $departmentId),
            'ambiental' => $this->buildEnvironmentalReport($baseInfo, $startDate, $endDate, $departmentId),
            'alertas' => $this->buildAlertsReport($baseInfo, $startDate, $endDate, $departmentId),
            default => $this->buildDepartmentalReport($baseInfo, $startDate, $endDate, $departmentId),
        };
    }

    /**
     * Reporte 1: Consolidado Departamental (22 departamentos de Guatemala).
     *
     * @param array<string, mixed> $baseInfo
     * @return array<string, mixed>
     */
    protected function buildDepartmentalReport(array $baseInfo, ?string $startDate, ?string $endDate, ?int $departmentId): array
    {
        $query = Department::query()->with([
            'solarFarms' => function ($farmQuery) use ($startDate, $endDate) {
                $farmQuery->with(['solarPanels'])
                    ->with(['energyGenerations' => function ($genQuery) use ($startDate, $endDate) {
                        if ($startDate) {
                            $genQuery->where('record_date', '>=', $startDate);
                        }
                        if ($endDate) {
                            $genQuery->where('record_date', '<=', $endDate);
                        }
                    }]);
            },
        ])->orderBy('name');

        if ($departmentId) {
            $query->where('id', $departmentId);
        }

        $departments = $query->get();

        $rows = [];
        $totalFarms = 0;
        $totalPanels = 0;
        $totalKw = 0.0;
        $totalKwh = 0.0;
        $totalCo2Kg = 0.0;
        $totalCo2Ton = 0.0;
        $totalFamilies = 0;

        foreach ($departments as $d) {
            $farmsCount = $d->solarFarms->count();
            $panelsCount = (int) $d->solarFarms->sum(fn ($f) => $f->solarPanels->sum('pivot.quantity'));
            $capacityKw = (float) $d->solarFarms->sum(fn ($f) => $f->calculated_capacity_kw);
            $genKwh = (float) $d->solarFarms->sum(fn ($f) => $f->energyGenerations->sum('real_kwh'));
            $co2Kg = $genKwh * self::CO2_FACTOR;
            $co2Ton = $co2Kg / 1000;
            $families = (int) $d->solarFarms->sum('benefited_families');

            $totalFarms += $farmsCount;
            $totalPanels += $panelsCount;
            $totalKw += $capacityKw;
            $totalKwh += $genKwh;
            $totalCo2Kg += $co2Kg;
            $totalCo2Ton += $co2Ton;
            $totalFamilies += $families;

            $rows[] = [
                'id' => $d->id,
                'name' => $d->name,
                'code' => $d->code,
                'farms_count' => $farmsCount,
                'panels_count' => $panelsCount,
                'capacity_kw' => $capacityKw,
                'generation_kwh' => $genKwh,
                'co2_ton' => $co2Ton,
                'co2_kg' => $co2Kg,
                'families' => $families,
            ];
        }

        return array_merge($baseInfo, [
            'title' => 'Reporte Consolidado Departamental',
            'subtitle' => 'Infraestructura, potencia instalada, generacion fotovoltaica y mitigacion de CO2 en los 22 departamentos',
            'columns' => [
                ['key' => 'name', 'label' => 'Departamento', 'align' => 'left'],
                ['key' => 'code', 'label' => 'Codigo', 'align' => 'center'],
                ['key' => 'farms_count', 'label' => 'Granjas', 'align' => 'center', 'format' => 'integer'],
                ['key' => 'panels_count', 'label' => 'Paneles', 'align' => 'right', 'format' => 'integer'],
                ['key' => 'capacity_kw', 'label' => 'Capacidad (kW)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'generation_kwh', 'label' => 'Generacion (kWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'families', 'label' => 'Familias', 'align' => 'right', 'format' => 'integer'],
                ['key' => 'co2_ton', 'label' => 'CO2 Evitado (Ton)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'co2_kg', 'label' => 'CO2 Evitado (kg)', 'align' => 'right', 'format' => 'decimal'],
            ],
            'rows' => $rows,
            'totals' => [
                'name' => 'TOTAL CONSOLIDADO',
                'code' => 'GT',
                'farms_count' => $totalFarms,
                'panels_count' => $totalPanels,
                'capacity_kw' => $totalKw,
                'generation_kwh' => $totalKwh,
                'families' => $totalFamilies,
                'co2_ton' => $totalCo2Ton,
                'co2_kg' => $totalCo2Kg,
            ],
            'summary_kpis' => [
                ['label' => 'Generacion Acumulada', 'value' => number_format($totalKwh, 0) . ' kWh', 'desc' => 'Energia limpia en el periodo'],
                ['label' => 'CO2 Evitado', 'value' => number_format($totalCo2Ton, 2) . ' Ton', 'desc' => 'Factor CNEE 0.40 kg/kWh'],
                ['label' => 'Capacidad Instalada', 'value' => number_format($totalKw, 2) . ' kW', 'desc' => 'Potencia total de inversores'],
                ['label' => 'Familias Beneficiadas', 'value' => number_format($totalFamilies, 0), 'desc' => 'Hogares suministrados'],
            ],
        ]);
    }

    /**
     * Reporte 2: Rendimiento y Generacion por Granja Solar.
     *
     * @param array<string, mixed> $baseInfo
     * @return array<string, mixed>
     */
    protected function buildFarmsReport(array $baseInfo, ?string $startDate, ?string $endDate, ?int $departmentId): array
    {
        $query = SolarFarm::query()->with(['department', 'solarPanels'])
            ->with(['energyGenerations' => function ($genQuery) use ($startDate, $endDate) {
                if ($startDate) {
                    $genQuery->where('record_date', '>=', $startDate);
                }
                if ($endDate) {
                    $genQuery->where('record_date', '<=', $endDate);
                }
            }])
            ->orderBy('department_id')
            ->orderBy('name');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $farms = $query->get();

        $rows = [];
        $totalKw = 0.0;
        $totalEstimated = 0.0;
        $totalReal = 0.0;
        $totalCo2Ton = 0.0;
        $totalPanels = 0;
        $totalFamilies = 0;

        foreach ($farms as $farm) {
            $capacityKw = (float) $farm->calculated_capacity_kw;
            $panelsCount = (int) $farm->solarPanels->sum('pivot.quantity');
            $estimatedKwh = (float) $farm->energyGenerations->sum('estimated_kwh');
            $realKwh = (float) $farm->energyGenerations->sum('real_kwh');
            $co2Ton = ($realKwh * self::CO2_FACTOR) / 1000;
            $performanceRatio = $estimatedKwh > 0 ? ($realKwh / $estimatedKwh) * 100 : 100.0;

            $totalKw += $capacityKw;
            $totalPanels += $panelsCount;
            $totalEstimated += $estimatedKwh;
            $totalReal += $realKwh;
            $totalCo2Ton += $co2Ton;
            $totalFamilies += (int) $farm->benefited_families;

            $rows[] = [
                'id' => $farm->id,
                'name' => $farm->name,
                'department' => $farm->department ? $farm->department->name : 'N/A',
                'status' => ucfirst((string) $farm->status),
                'panels_count' => $panelsCount,
                'capacity_kw' => $capacityKw,
                'estimated_kwh' => $estimatedKwh,
                'real_kwh' => $realKwh,
                'performance_pct' => $performanceRatio,
                'co2_ton' => $co2Ton,
                'families' => (int) $farm->benefited_families,
            ];
        }

        $globalPerformance = $totalEstimated > 0 ? ($totalReal / $totalEstimated) * 100 : 100.0;

        return array_merge($baseInfo, [
            'title' => 'Reporte de Rendimiento por Granja Solar',
            'subtitle' => 'Generacion real vs. estimada, potencia nominal instalada y factor de rendimiento por planta',
            'columns' => [
                ['key' => 'name', 'label' => 'Granja Solar', 'align' => 'left'],
                ['key' => 'department', 'label' => 'Departamento', 'align' => 'left'],
                ['key' => 'status', 'label' => 'Estado', 'align' => 'center'],
                ['key' => 'panels_count', 'label' => 'Paneles', 'align' => 'right', 'format' => 'integer'],
                ['key' => 'capacity_kw', 'label' => 'Capacidad (kW)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'estimated_kwh', 'label' => 'Estimada (kWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'real_kwh', 'label' => 'Real (kWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'performance_pct', 'label' => 'Rendimiento (%)', 'align' => 'right', 'format' => 'percentage'],
                ['key' => 'co2_ton', 'label' => 'CO2 Evitado (Ton)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'families', 'label' => 'Familias', 'align' => 'right', 'format' => 'integer'],
            ],
            'rows' => $rows,
            'totals' => [
                'name' => 'TOTAL ' . count($rows) . ' GRANJAS',
                'department' => '-',
                'status' => 'Operativo',
                'panels_count' => $totalPanels,
                'capacity_kw' => $totalKw,
                'estimated_kwh' => $totalEstimated,
                'real_kwh' => $totalReal,
                'performance_pct' => $globalPerformance,
                'co2_ton' => $totalCo2Ton,
                'families' => $totalFamilies,
            ],
            'summary_kpis' => [
                ['label' => 'Granjas Monitoreadas', 'value' => (string) count($rows), 'desc' => 'Plantas en el filtro'],
                ['label' => 'Generacion Real Total', 'value' => number_format($totalReal, 0) . ' kWh', 'desc' => 'Produccion fotovoltaica'],
                ['label' => 'Rendimiento Global', 'value' => number_format($globalPerformance, 1) . '%', 'desc' => 'Generacion real vs. estimada'],
                ['label' => 'CO2 Mitigado', 'value' => number_format($totalCo2Ton, 2) . ' Ton', 'desc' => 'Aporte ambiental neto'],
            ],
        ]);
    }

    /**
     * Reporte 3: Balance Ecologico y Mitigacion de CO2.
     *
     * @param array<string, mixed> $baseInfo
     * @return array<string, mixed>
     */
    protected function buildEnvironmentalReport(array $baseInfo, ?string $startDate, ?string $endDate, ?int $departmentId): array
    {
        $query = Department::query()->with([
            'solarFarms' => function ($farmQuery) use ($startDate, $endDate) {
                $farmQuery->with(['energyGenerations' => function ($genQuery) use ($startDate, $endDate) {
                    if ($startDate) {
                        $genQuery->where('record_date', '>=', $startDate);
                    }
                    if ($endDate) {
                        $genQuery->where('record_date', '<=', $endDate);
                    }
                }]);
            },
        ])->orderBy('name');

        if ($departmentId) {
            $query->where('id', $departmentId);
        }

        $departments = $query->get();

        $rows = [];
        $totalKwh = 0.0;
        $totalCo2Kg = 0.0;
        $totalCo2Ton = 0.0;
        $totalTrees = 0;
        $totalFamilies = 0;
        $totalGasolineGallons = 0.0;

        foreach ($departments as $d) {
            $kwh = (float) $d->solarFarms->sum(fn ($f) => $f->energyGenerations->sum('real_kwh'));
            $co2Kg = $kwh * self::CO2_FACTOR;
            $co2Ton = $co2Kg / 1000;
            // 1 arbol adulto absorbe ~20 kg CO2/ano
            $treesEquivalent = (int) round($co2Kg / 20);
            // 1 galon de gasolina equivale a ~8.887 kg de CO2
            $gasolineGallons = $co2Kg / 8.887;
            $families = (int) $d->solarFarms->sum('benefited_families');

            $totalKwh += $kwh;
            $totalCo2Kg += $co2Kg;
            $totalCo2Ton += $co2Ton;
            $totalTrees += $treesEquivalent;
            $totalFamilies += $families;
            $totalGasolineGallons += $gasolineGallons;

            $rows[] = [
                'id' => $d->id,
                'name' => $d->name,
                'generation_kwh' => $kwh,
                'generation_mwh' => $kwh / 1000,
                'co2_kg' => $co2Kg,
                'co2_ton' => $co2Ton,
                'trees_equivalent' => $treesEquivalent,
                'gasoline_gallons' => $gasolineGallons,
                'families' => $families,
            ];
        }

        return array_merge($baseInfo, [
            'title' => 'Reporte Ambiental y Mitigacion de CO2',
            'subtitle' => 'Balance ecologico territorial, emisiones de gases de efecto invernadero evitadas y equivalencias forestales',
            'columns' => [
                ['key' => 'name', 'label' => 'Departamento', 'align' => 'left'],
                ['key' => 'generation_kwh', 'label' => 'Energia Limpia (kWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'generation_mwh', 'label' => 'Energia (MWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'co2_kg', 'label' => 'CO2 Evitado (kg)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'co2_ton', 'label' => 'CO2 Evitado (Ton)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'trees_equivalent', 'label' => 'Arboles Equivalentes', 'align' => 'right', 'format' => 'integer'],
                ['key' => 'gasoline_gallons', 'label' => 'Galones Gasolina Evitados', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'families', 'label' => 'Hogares Suministrados', 'align' => 'right', 'format' => 'integer'],
            ],
            'rows' => $rows,
            'totals' => [
                'name' => 'TOTAL NACIONAL',
                'generation_kwh' => $totalKwh,
                'generation_mwh' => $totalKwh / 1000,
                'co2_kg' => $totalCo2Kg,
                'co2_ton' => $totalCo2Ton,
                'trees_equivalent' => $totalTrees,
                'gasoline_gallons' => $totalGasolineGallons,
                'families' => $totalFamilies,
            ],
            'summary_kpis' => [
                ['label' => 'Total CO2 Mitigado', 'value' => number_format($totalCo2Ton, 2) . ' Ton', 'desc' => 'Factor CNEE: 0.40 kg/kWh'],
                ['label' => 'Energia Limpia', 'value' => number_format($totalKwh / 1000, 2) . ' MWh', 'desc' => 'Generacion renovable neta'],
                ['label' => 'Arboles Equivalentes', 'value' => number_format($totalTrees, 0), 'desc' => 'Capacidad de absorcion anual'],
                ['label' => 'Gasolina Sustituida', 'value' => number_format($totalGasolineGallons, 0) . ' gal', 'desc' => 'Combustibles fosiles evitados'],
            ],
        ]);
    }

    /**
     * Reporte 4: Auditoria de Incidentes y Desviaciones Operativas (Alertas).
     *
     * @param array<string, mixed> $baseInfo
     * @return array<string, mixed>
     */
    protected function buildAlertsReport(array $baseInfo, ?string $startDate, ?string $endDate, ?int $departmentId): array
    {
        $query = GenerationAlert::query()->with(['solarFarm.department', 'resolvedBy'])
            ->orderByDesc('created_at');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }
        if ($departmentId) {
            $query->whereHas('solarFarm', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $alerts = $query->get();

        $rows = [];
        $activeCount = 0;
        $resolvedCount = 0;
        $totalDeviation = 0.0;

        foreach ($alerts as $a) {
            $isResolved = in_array($a->status, ['resolved', 'resuelta'], true);
            if ($isResolved) {
                $resolvedCount++;
            } else {
                $activeCount++;
            }
            $dev = (float) $a->deviation_percentage;
            $totalDeviation += $dev;

            $rows[] = [
                'id' => $a->id,
                'farm' => $a->solarFarm ? $a->solarFarm->name : 'N/A',
                'department' => ($a->solarFarm && $a->solarFarm->department) ? $a->solarFarm->department->name : 'N/A',
                'period' => $a->period,
                'detected_at' => $a->created_at ? $a->created_at->timezone('America/Guatemala')->format('d/m/Y H:i') : '-',
                'estimated_kwh' => (float) $a->estimated_kwh,
                'real_kwh' => (float) $a->real_kwh,
                'deviation_pct' => $dev,
                'status' => $isResolved ? 'Resuelta' : 'Activa',
                'resolved_by' => $a->resolvedBy ? $a->resolvedBy->name : '-',
                'resolution_notes' => $a->resolution_notes ?: ($isResolved ? 'Resuelta' : 'Pendiente de inspeccion en campo'),
            ];
        }

        $avgDeviation = count($alerts) > 0 ? $totalDeviation / count($alerts) : 0.0;

        return array_merge($baseInfo, [
            'title' => 'Reporte de Incidentes y Alertas Operativas',
            'subtitle' => 'Bitacora de fallas, perdidas de generacion (deficit >= 20%) y ciclo de resolucion con notas tecnicas',
            'columns' => [
                ['key' => 'farm', 'label' => 'Granja Solar', 'align' => 'left'],
                ['key' => 'department', 'label' => 'Departamento', 'align' => 'left'],
                ['key' => 'period', 'label' => 'Periodo', 'align' => 'center'],
                ['key' => 'detected_at', 'label' => 'Fecha Deteccion', 'align' => 'center'],
                ['key' => 'estimated_kwh', 'label' => 'Esperada (kWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'real_kwh', 'label' => 'Real (kWh)', 'align' => 'right', 'format' => 'decimal'],
                ['key' => 'deviation_pct', 'label' => 'Deficit (%)', 'align' => 'right', 'format' => 'percentage'],
                ['key' => 'status', 'label' => 'Estado', 'align' => 'center'],
                ['key' => 'resolved_by', 'label' => 'Resuelto Por', 'align' => 'left'],
                ['key' => 'resolution_notes', 'label' => 'Causa Tecnica / Notas', 'align' => 'left'],
            ],
            'rows' => $rows,
            'totals' => [
                'farm' => 'TOTAL ALERTAS: ' . count($alerts),
                'department' => '-',
                'period' => '-',
                'detected_at' => '-',
                'estimated_kwh' => (float) $alerts->sum('estimated_kwh'),
                'real_kwh' => (float) $alerts->sum('real_kwh'),
                'deviation_pct' => $avgDeviation,
                'status' => $activeCount . ' activas / ' . $resolvedCount . ' resueltas',
                'resolved_by' => '-',
                'resolution_notes' => '-',
            ],
            'summary_kpis' => [
                ['label' => 'Alertas Totales', 'value' => (string) count($alerts), 'desc' => 'Eventos con deficit >= 20%'],
                ['label' => 'Alertas Resueltas', 'value' => (string) $resolvedCount, 'desc' => 'Trazabilidad y notas completadas'],
                ['label' => 'Alertas Activas', 'value' => (string) $activeCount, 'desc' => 'Requieren atencion tecnica'],
                ['label' => 'Deficit Promedio', 'value' => number_format($avgDeviation, 1) . '%', 'desc' => 'Desviacion en anomalias'],
            ],
        ]);
    }

    /**
     * Construye un archivo Excel enriquecido (.xls HTML Spreadsheet) mediante la plantilla Blade institucional.
     *
     * @param array<string, mixed> $data
     */
    public function buildExcelHtml(array $data): string
    {
        return "\xEF\xBB\xBF" . view('reports.excel', $data)->render();
    }

    /**
     * Construye un archivo CSV plano con BOM UTF-8 para apertura directa y limpia en cualquier version de Excel.
     *
     * @param array<string, mixed> $data
     */
    public function buildCsv(array $data): string
    {
        $columns = $data['columns'];
        $rows = $data['rows'];
        $totals = $data['totals'] ?? [];

        $fp = fopen('php://temp', 'r+');
        fputs($fp, "\xEF\xBB\xBF"); // BOM UTF-8

        // Titulo del reporte
        fputcsv($fp, ["K'IN SOLAR GUATEMALA - " . $data['title']]);
        fputcsv($fp, ["Ambito: " . $data['department_name'], "Periodo: " . ($data['start_date'] ?: 'Inicio') . ' al ' . ($data['end_date'] ?: 'Hoy')]);
        fputcsv($fp, ["Generado el: " . $data['generated_at'], "Por: " . $data['generated_by'] . ' (' . $data['user_role'] . ')']);
        fputcsv($fp, []); // linea vacia

        // Encabezados
        $headerLabels = array_merge(['#'], array_map(fn ($c) => $c['label'], $columns));
        fputcsv($fp, $headerLabels);

        // Filas
        foreach ($rows as $i => $row) {
            $line = [$i + 1];
            foreach ($columns as $col) {
                $val = $row[$col['key']] ?? '';
                $format = $col['format'] ?? 'string';
                $line[] = match ($format) {
                    'decimal' => is_numeric($val) ? number_format((float) $val, 2, '.', '') : $val,
                    'integer' => is_numeric($val) ? (int) $val : $val,
                    'percentage' => is_numeric($val) ? number_format((float) $val, 1, '.', '') . '%' : $val,
                    default => (string) $val,
                };
            }
            fputcsv($fp, $line);
        }

        // Totales
        if (!empty($totals)) {
            $totalLine = ['TOTAL'];
            foreach ($columns as $col) {
                $val = $totals[$col['key']] ?? '';
                $format = $col['format'] ?? 'string';
                $totalLine[] = match ($format) {
                    'decimal' => is_numeric($val) ? number_format((float) $val, 2, '.', '') : $val,
                    'integer' => is_numeric($val) ? (int) $val : $val,
                    'percentage' => is_numeric($val) ? number_format((float) $val, 1, '.', '') . '%' : $val,
                    default => (string) $val,
                };
            }
            fputcsv($fp, $totalLine);
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        return (string) $csv;
    }
}
