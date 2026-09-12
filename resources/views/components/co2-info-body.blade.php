@props([
    'kwh' => null,
])

@php
    $factor = (float) config('solar.co2_kg_per_kwh');
    $kwhValue = $kwh !== null ? (float) $kwh : null;
    $kgValue = $kwhValue !== null ? $kwhValue * $factor : null;
@endphp

<div class="kin-co2-title">¿Cómo se calcula el CO₂ evitado?</div>
<div class="kin-co2-formula">CO₂ evitado (kg) = kWh generados × {{ number_format($factor, 2) }}</div>
<div class="kin-co2-formula">Toneladas = kg ÷ 1,000</div>
@if($kgValue !== null)
    <div class="kin-co2-example">
        <span>Con este valor:</span>
        {{ number_format($kwhValue, 0) }} kWh × {{ number_format($factor, 2) }} =
        <strong>{{ number_format($kgValue, 0) }} kg</strong>
        = <strong>{{ number_format($kgValue / 1000, 1) }} t</strong>
    </div>
@endif
<p class="kin-co2-text">
    El factor de <strong>{{ number_format($factor, 2) }} kg CO₂/kWh</strong> lo fijan las bases de la competencia
    (§14, «Datos y reglas de cálculo»): es la emisión de la generación térmica de la red que cada kWh solar sustituye.
    El sistema lo aplica en el servidor al registrar cada medición, así que la cifra es la misma en dashboard, mapa,
    reportes y API.
</p>
