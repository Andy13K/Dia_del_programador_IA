<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportData['title'] }} — K'in Solar Guatemala</title>
    <link rel="icon" type="image/png" href="/images/kin-icon-dorado.png">
    {{--
        OWASP A02: el CSP del sistema (ver app/Http/Middleware/SecurityHeaders.php) solo permite
        script-src 'self', unpkg.com y cdn.jsdelivr.net — cdn.tailwindcss.com NO esta en la lista,
        asi que ese script quedaba bloqueado en silencio y esta pagina se veia sin ningun estilo,
        tanto en pantalla como al imprimir/guardar PDF. Se reemplaza por el CSS ya compilado del
        propio sistema (mismo bundle que usa toda la app), que ya incluye todas las clases de
        Tailwind usadas en esta plantilla.
    --}}
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            background-color: #f8fafc;
        }
        @media print {
            @page {
                size: landscape;
                margin: 12mm;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- BARRA FLOTANTE DE CONTROL (NO SE IMPRIME) -->
    <div class="no-print max-w-7xl mx-auto mb-6 bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-4 border border-slate-800">
        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.index', ['type' => $reportData['type'], 'start_date' => $reportData['start_date'], 'end_date' => $reportData['end_date'], 'department_id' => $reportData['department_id']]) }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition flex items-center space-x-2">
                <span>← Volver al Panel</span>
            </a>
            <span class="text-xs text-slate-400">|</span>
            <span class="text-xs text-slate-300 font-medium">Plantilla Ejecutiva Oficial para PDF / Impresión</span>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.export.excel', ['type' => $reportData['type'], 'start_date' => $reportData['start_date'], 'end_date' => $reportData['end_date'], 'department_id' => $reportData['department_id']]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition flex items-center space-x-1.5 shadow-sm">
                <span>Descargar Excel (.xls)</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold rounded-xl transition flex items-center space-x-2 shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Guardar como PDF / Imprimir</span>
            </button>
        </div>
    </div>

    <!-- DOCUMENTO EJECUTIVO DE REPORTE -->
    <main class="print-page max-w-7xl mx-auto bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-200">

        <!-- ENCABEZADO INSTITUCIONAL -->
        <header class="border-b-2 border-slate-900 pb-6 mb-6">
            <div class="flex items-start justify-between gap-6">
                <!-- Logos y Título -->
                <div class="flex items-center space-x-4">
                    <img src="/images/kin-icon-dorado.png" alt="K'in Solar" class="w-16 h-16 rounded-2xl shadow-sm border border-amber-500/30">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-widest text-amber-600">
                            República de Guatemala • CNEE & MEM
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight leading-none mt-1">
                            K'IN SOLAR GUATEMALA
                        </h1>
                        <p class="text-xs font-semibold text-slate-600 mt-1">
                            Sistema Nacional de Monitoreo, Trazabilidad y Proyección Fotovoltaica
                        </p>
                    </div>
                </div>

                <!-- Título del Reporte y Estado -->
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-slate-900 text-white font-mono text-[10px] font-bold rounded-lg uppercase tracking-wider">
                        Documento Oficial
                    </span>
                    <h2 class="text-lg font-black text-slate-900 mt-2">{{ $reportData['title'] }}</h2>
                    <p class="text-xs text-slate-500 font-medium">{{ $reportData['subtitle'] }}</p>
                </div>
            </div>

            <!-- Cuadro de Metadatos de Auditoría -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 pt-4 border-t border-slate-200 text-xs">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block font-mono">Ámbito Geográfico</span>
                    <span class="font-bold text-slate-900">{{ $reportData['department_name'] }}</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block font-mono">Período Evaluado</span>
                    <span class="font-bold text-slate-900">{{ $reportData['start_date'] ?: 'Histórico inicial' }} al {{ $reportData['end_date'] ?: 'Actual' }}</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block font-mono">Fecha de Emisión</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $reportData['generated_at'] }} (UTC-6)</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block font-mono">Usuario / Rol</span>
                    <span class="font-bold text-slate-900">{{ $reportData['generated_by'] }} ({{ $reportData['user_role'] }})</span>
                </div>
            </div>
        </header>

        <!-- TARJETAS RESUMEN KPIS -->
        @if(!empty($reportData['summary_kpis']))
            <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @foreach($reportData['summary_kpis'] as $kpi)
                    <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/70">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 block">{{ $kpi['label'] }}</span>
                        <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $kpi['value'] }}</div>
                        <span class="text-[10px] text-amber-700 block mt-0.5">{{ $kpi['desc'] }}</span>
                    </div>
                @endforeach
            </section>
        @endif

        <!-- TABLA PRINCIPAL DE DATOS -->
        <section class="overflow-x-auto rounded-2xl border border-slate-300">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-900 text-white uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="p-3 text-center border-b border-slate-700 w-10">#</th>
                        @foreach($reportData['columns'] as $col)
                            <th class="p-3 border-b border-slate-700 {{ $col['align'] === 'right' ? 'text-right' : ($col['align'] === 'center' ? 'text-center' : 'text-left') }}">
                                {{ $col['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($reportData['rows'] as $idx => $row)
                        <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/70' }}">
                            <td class="p-3 text-center font-mono text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                            @foreach($reportData['columns'] as $col)
                                @php
                                    $val = $row[$col['key']] ?? '';
                                    $align = $col['align'] ?? 'left';
                                    $format = $col['format'] ?? 'string';
                                    $alignClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
                                @endphp
                                <td class="p-3 {{ $alignClass }} {{ $align === 'right' ? 'font-mono' : '' }}">
                                    @if($format === 'decimal' && is_numeric($val))
                                        <span class="font-semibold text-slate-900">{{ number_format((float)$val, 2) }}</span>
                                    @elseif($format === 'integer' && is_numeric($val))
                                        <span class="font-semibold text-slate-900">{{ number_format((int)$val) }}</span>
                                    @elseif($format === 'percentage' && is_numeric($val))
                                        <span class="font-bold text-amber-600">{{ number_format((float)$val, 1) }}%</span>
                                    @else
                                        <span class="text-slate-800">{{ $val }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                @if(!empty($reportData['totals']))
                    <tfoot class="bg-slate-100 border-t-2 border-slate-900 font-black text-slate-950">
                        <tr>
                            <td class="p-3 text-center">-</td>
                            @foreach($reportData['columns'] as $col)
                                @php
                                    $val = $reportData['totals'][$col['key']] ?? '';
                                    $align = $col['align'] ?? 'left';
                                    $format = $col['format'] ?? 'string';
                                    $alignClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
                                @endphp
                                <td class="p-3 {{ $alignClass }} {{ $align === 'right' ? 'font-mono' : '' }}">
                                    @if($format === 'decimal' && is_numeric($val))
                                        {{ number_format((float)$val, 2) }}
                                    @elseif($format === 'integer' && is_numeric($val))
                                        {{ number_format((int)$val) }}
                                    @elseif($format === 'percentage' && is_numeric($val))
                                        {{ number_format((float)$val, 1) }}%
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </section>

        <!-- SECCIÓN DE FIRMAS Y VALIDEZ LEGAL -->
        <footer class="mt-12 pt-8 border-t border-slate-300">
            <div class="grid grid-cols-2 gap-12 text-center text-xs">
                <div>
                    <div class="border-b border-slate-400 w-3/4 mx-auto mb-2 h-16"></div>
                    <span class="font-extrabold text-slate-900 block">Ing. Supervisor de Operaciones Energéticas</span>
                    <span class="text-slate-500 text-[10px]">K'in Solar Guatemala • Verificación Técnica</span>
                </div>
                <div>
                    <div class="border-b border-slate-400 w-3/4 mx-auto mb-2 h-16"></div>
                    <span class="font-extrabold text-slate-900 block">Dirección de Sostenibilidad y Recursos Renovables</span>
                    <span class="text-slate-500 text-[10px]">CNEE / Ministerio de Energía y Minas</span>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-slate-200 text-[10px] text-slate-400 flex flex-col sm:flex-row justify-between items-center gap-2">
                <span>Certificación de emisiones bajo factor normativo CNEE de 0.40 kg CO₂ evitado por kWh.</span>
                <span class="font-mono">Plataforma Oficial: https://kin-solar-guatemala.duckdns.org</span>
            </div>
        </footer>

    </main>

    @if($autoPrint)
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    window.print();
                }, 300);
            });
        </script>
    @endif
</body>
</html>
