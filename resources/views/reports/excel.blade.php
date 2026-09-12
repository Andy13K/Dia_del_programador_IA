<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <{{ 'x' }}:ExcelWorkbook>
            <{{ 'x' }}:ExcelWorksheets>
                <{{ 'x' }}:ExcelWorksheet>
                    <{{ 'x' }}:Name>K'in Solar Reporte</{{ 'x' }}:Name>
                    <{{ 'x' }}:WorksheetOptions>
                        <{{ 'x' }}:DisplayGridlines/>
                    </{{ 'x' }}:WorksheetOptions>
                </{{ 'x' }}:ExcelWorksheet>
            </{{ 'x' }}:ExcelWorksheets>
        </{{ 'x' }}:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; color: #1e293b; background: #ffffff; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .brand-title { font-size: 16pt; font-weight: bold; background-color: #0f172a; color: #f8fafc; padding: 12px; text-align: left; }
        .brand-subtitle { font-size: 11pt; font-weight: bold; background-color: #1e293b; color: #fbbf24; padding: 8px 12px; text-align: left; }
        .meta-label { font-size: 9.5pt; font-weight: bold; color: #475569; background-color: #f1f5f9; padding: 6px 10px; border: 1px solid #e2e8f0; }
        .meta-value { font-size: 10pt; font-weight: bold; color: #0f172a; padding: 6px 10px; border: 1px solid #e2e8f0; }
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .kpi-cell { background-color: #fffbeb; border: 2px solid #fde68a; padding: 10px; text-align: center; }
        .kpi-val { font-size: 14pt; font-weight: bold; color: #b45309; }
        .kpi-lbl { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #78350f; }
        .kpi-desc { font-size: 7.5pt; color: #92400e; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 10pt; border: 1px solid #334155; padding: 10px 8px; text-align: center; }
        .data-table td { border: 1px solid #cbd5e1; padding: 8px 6px; font-size: 9.5pt; }
        .row-even { background-color: #f8fafc; }
        .row-odd { background-color: #ffffff; }
        .data-table tfoot td { background-color: #e2e8f0; font-weight: bold; font-size: 10pt; border: 2px solid #0f172a; padding: 10px 8px; color: #0f172a; }
        .num-dec { mso-number-format:"\#\,\#\#0\.00"; text-align: right; }
        .num-int { mso-number-format:"\#\,\#\#0"; text-align: right; }
        .num-pct { mso-number-format:"0\.0%"; text-align: right; }
        .txt-center { text-align: center; }
        .txt-left { text-align: left; }
        .txt-right { text-align: right; }
        .footer-note { font-size: 8.5pt; color: #64748b; font-style: italic; padding-top: 15px; }
    </style>
</head>
<body>
    @php
        $colCount = count($columns) + 1;
        $kpis = $summary_kpis ?? [];
        // Logo embebido como data URI: el archivo .xls se descarga y se abre de forma
        // desconectada del servidor, así que una <img src="/images/..."> normal no cargaría
        // en Excel. Al embeberlo en base64 el logo se ve siempre, sin depender de internet.
        $logoBase64 = base64_encode((string) file_get_contents(public_path('images/kin-icon-dorado.png')));
    @endphp

    <!-- Cabecera Institucional -->
    <table class="header-table">
        <tr>
            <td colspan="{{ $colCount }}" class="brand-title" style="padding: 6px 12px;">
                <table style="border-collapse: collapse;"><tr>
                    <td style="padding: 0 10px 0 0; vertical-align: middle; border: none;">
                        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="K'in Solar" width="34" height="34" style="display: block;">
                    </td>
                    <td style="vertical-align: middle; border: none; background-color: transparent; padding: 0;">
                        K'IN SOLAR GUATEMALA — SISTEMA NACIONAL DE MONITOREO FOTOVOLTAICO
                    </td>
                </tr></table>
            </td>
        </tr>
        <tr>
            <td colspan="{{ $colCount }}" class="brand-subtitle">
                {{ $title }} • {{ $subtitle }}
            </td>
        </tr>
        <tr><td colspan="{{ $colCount }}">&nbsp;</td></tr>
        <tr>
            <td colspan="2" class="meta-label">Ámbito Territorial:</td>
            <td colspan="2" class="meta-value">{{ $department_name }}</td>
            <td colspan="2" class="meta-label">Período Evaluado:</td>
            <td colspan="{{ max(1, $colCount - 6) }}" class="meta-value">{{ $start_date ?: 'Inicio histórico' }} al {{ $end_date ?: 'Fecha actual' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Fecha de Emisión:</td>
            <td colspan="2" class="meta-value">{{ $generated_at }} (Hora de Guatemala)</td>
            <td colspan="2" class="meta-label">Emitido Por:</td>
            <td colspan="{{ max(1, $colCount - 6) }}" class="meta-value">{{ $generated_by }} ({{ $user_role }})</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Factor Normativo CO₂:</td>
            <td colspan="{{ $colCount - 2 }}" class="meta-value">0.40 kg CO₂ evitado / kWh generado (Norma CNEE / Ministerio de Energía y Minas)</td>
        </tr>
        <tr><td colspan="{{ $colCount }}">&nbsp;</td></tr>

        @if(!empty($kpis))
            <tr>
                @php $kpiSpan = max(1, (int) floor($colCount / count($kpis))); @endphp
                @foreach($kpis as $kpi)
                    <td colspan="{{ $kpiSpan }}" class="kpi-cell">
                        <div class="kpi-lbl">{{ $kpi['label'] }}</div>
                        <div class="kpi-val">{{ $kpi['value'] }}</div>
                        <div class="kpi-desc">{{ $kpi['desc'] }}</div>
                    </td>
                @endforeach
            </tr>
            <tr><td colspan="{{ $colCount }}">&nbsp;</td></tr>
        @endif
    </table>

    <!-- Tabla Principal de Datos -->
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                @foreach($columns as $col)
                    <th>{{ $col['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $idx => $row)
                <tr class="{{ $idx % 2 === 0 ? 'row-even' : 'row-odd' }}">
                    <td class="txt-center">{{ $idx + 1 }}</td>
                    @foreach($columns as $col)
                        @php
                            $val = $row[$col['key']] ?? '';
                            $align = $col['align'] ?? 'left';
                            $format = $col['format'] ?? 'string';
                            $cssClass = match ($align) {
                                'right' => ($format === 'decimal') ? 'num-dec' : (($format === 'integer') ? 'num-int' : 'txt-right'),
                                'center' => 'txt-center',
                                default => 'txt-left',
                            };
                        @endphp
                        <td class="{{ $cssClass }}">
                            @if($format === 'decimal' && is_numeric($val))
                                {{ number_format((float)$val, 2, '.', '') }}
                            @elseif($format === 'integer' && is_numeric($val))
                                {{ (int)$val }}
                            @elseif($format === 'percentage' && is_numeric($val))
                                {{ number_format((float)$val, 1, '.', '') }}%
                            @else
                                {{ $val }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        @if(!empty($totals))
            <tfoot>
                <tr>
                    <td class="txt-center">-</td>
                    @foreach($columns as $col)
                        @php
                            $val = $totals[$col['key']] ?? '';
                            $align = $col['align'] ?? 'left';
                            $format = $col['format'] ?? 'string';
                            $cssClass = match ($align) {
                                'right' => ($format === 'decimal') ? 'num-dec' : (($format === 'integer') ? 'num-int' : 'txt-right'),
                                'center' => 'txt-center',
                                default => 'txt-left',
                            };
                        @endphp
                        <td class="{{ $cssClass }}">
                            <strong>
                                @if($format === 'decimal' && is_numeric($val))
                                    {{ number_format((float)$val, 2, '.', '') }}
                                @elseif($format === 'integer' && is_numeric($val))
                                    {{ (int)$val }}
                                @elseif($format === 'percentage' && is_numeric($val))
                                    {{ number_format((float)$val, 1, '.', '') }}%
                                @else
                                    {{ $val }}
                                @endif
                            </strong>
                        </td>
                    @endforeach
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer-note">
        * Documento oficial emitido por la plataforma K'in Solar Guatemala. Verificación en línea: https://kin-solar-guatemala.duckdns.org • Factor normativo CNEE: 0.40 kg CO₂ evitado por kWh generado.
    </p>
</body>
</html>
