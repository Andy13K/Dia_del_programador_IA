{{--
    Botón "!" junto a cualquier cifra de CO₂ evitado. Al pulsarlo muestra la fórmula y su base.
    - kwh: si se pasa, el panel muestra el cálculo concreto de esa cifra.
    El comportamiento (abrir/cerrar, Escape, clic afuera, posicionamiento fijo para no quedar
    recortado por tarjetas con overflow-hidden o tablas con scroll) está en resources/js/app.js.
--}}
@props([
    'kwh' => null,
])

@php $panelId = 'co2-info-'.uniqid(); @endphp

<span {{ $attributes->class(['kin-co2']) }} data-co2>
    <button type="button" class="kin-co2-btn" data-co2-toggle aria-expanded="false" aria-controls="{{ $panelId }}"
            aria-label="Cómo se calcula el CO₂ evitado" title="¿Cómo se calcula?">!</button>
    <div id="{{ $panelId }}" class="kin-co2-panel" role="dialog" aria-label="Cómo se calcula el CO₂ evitado" hidden>
        <x-co2-info-body :kwh="$kwh" />
    </div>
</span>
