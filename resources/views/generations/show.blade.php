@extends('layouts.app', ['title' => 'Detalle de Medición'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('generations.index') }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver al Historial</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Ficha de Medición Fotovoltaica #{{ $generation->id }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Detalle de registro para el período {{ $generation->period }}.
            </p>
        </div>
        <a href="{{ route('farms.show', $generation->solar_farm_id) }}" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            Ver Granja Solar &rarr;
        </a>
    </div>

    @php
        $diff = (float)$generation->real_kwh - (float)$generation->estimated_kwh;
        $pct = (float)$generation->estimated_kwh > 0 ? ($diff / (float)$generation->estimated_kwh) * 100 : 0;
        $co2Ton = (float)$generation->co2_kg / 1000;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card>
            <span class="text-xs text-slate-400 font-semibold uppercase">Generación Esperada</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                {{ number_format((float)$generation->estimated_kwh, 1) }}
            </div>
            <span class="text-[11px] text-slate-500">kWh programados</span>
        </x-card>

        <x-card>
            <span class="text-xs text-slate-400 font-semibold uppercase">Generación Real</span>
            <div class="text-2xl font-black {{ $pct <= -20 ? 'text-rose-600' : 'text-slate-900 dark:text-white' }} mt-1 font-mono">
                {{ number_format((float)$generation->real_kwh, 1) }}
            </div>
            <span class="text-[11px] {{ $pct < 0 ? 'text-rose-500' : 'text-emerald-500' }} font-bold">
                {{ $pct > 0 ? '+'.number_format($pct, 1) : number_format($pct, 1) }}% de variación
            </span>
        </x-card>

        <x-card>
            <span class="text-xs text-slate-400 font-semibold uppercase">CO₂ Evitado<x-co2-info :kwh="(float) $generation->real_kwh" /></span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 font-mono">
                {{ number_format($co2Ton, 2) }} Ton
            </div>
            <span class="text-[11px] text-slate-500">{{ number_format((float)$generation->co2_kg, 1) }} kg certificados</span>
        </x-card>
    </div>

    <x-card>
        <div class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-slate-400 block font-semibold">Granja Solar:</span>
                    <strong class="text-slate-900 dark:text-white text-sm">{{ $generation->solarFarm->name ?? 'N/A' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Período de Medición:</span>
                    <strong class="text-slate-900 dark:text-white text-sm font-mono">{{ $generation->period }}</strong>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-slate-400 block font-semibold">Fecha de Registro:</span>
                    <strong class="text-slate-800 dark:text-slate-200">{{ $generation->record_date }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Estado de Alerta (RF-14):</span>
                    @if($generation->generationAlert)
                        <span class="inline-flex items-center gap-1 font-bold text-rose-600">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span>Déficit del {{ $generation->generationAlert->deviation_percentage }}% (Alerta {{ $generation->generationAlert->status }})</span>
                        </span>
                    @else
                        <span class="text-emerald-600 font-bold">Producción Normal (&gt;80% esperado)</span>
                    @endif
                </div>
            </div>

            @if($generation->notes)
                <div>
                    <span class="text-slate-400 block font-semibold mb-1">Notas y Observaciones:</span>
                    <p class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 italic">
                        "{{ $generation->notes }}"
                    </p>
                </div>
            @endif
        </div>
    </x-card>

</div>
@endsection
