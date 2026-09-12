@extends('layouts.app', ['title' => 'Alertas y seguimiento'])
@section('content')
<div class="space-y-6">
    <div class="kin-dashboard-heading">
        <div><div class="kin-eyebrow">SUPERVISIÓN · GENERACIÓN SOLAR</div><h1>Detectar. Entender. Resolver.</h1><p>Da seguimiento a las desviaciones de generación y registra su resolución técnica.</p></div>
        <x-button :href="route('map.index')" variant="outline" icon="map">Ver mapa solar</x-button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-kpi-card title="Requieren atención" :value="$stats['active'] ?? 0" subtitle="Alertas activas" variant="alert" icon="triangle-alert"/>
        <x-kpi-card title="Seguimiento completado" :value="$stats['resolved'] ?? 0" subtitle="Alertas resueltas" variant="eco" icon="circle-check"/>
        <x-kpi-card title="Historial de incidencias" :value="$stats['total'] ?? 0" subtitle="Todas las alertas" variant="indigo" icon="history"/>
    </div>
    <section class="kin-panel p-4 sm:p-6">
        <form method="GET" action="{{ route('alerts.index') }}" class="flex flex-col sm:flex-row gap-3 mb-6">
            <div class="flex-1"><label for="alert-farm" class="block text-xs mb-2">Granja solar</label><select id="alert-farm" name="solar_farm_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"><option value="">Todas las granjas</option>@foreach($farms as $farm)<option value="{{ $farm->id }}" @selected(($filters['solar_farm_id'] ?? '') == $farm->id)>{{ $farm->name }}</option>@endforeach</select></div>
            <div class="sm:w-48"><label for="alert-status" class="block text-xs mb-2">Estado</label><select id="alert-status" name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"><option value="">Todos los estados</option><option value="active" @selected(($filters['status'] ?? '') === 'active')>Activa</option><option value="resolved" @selected(($filters['status'] ?? '') === 'resolved')>Resuelta</option></select></div>
            <div class="flex items-end"><x-button type="submit" variant="outline" icon="sliders-horizontal">Filtrar</x-button></div>
        </form>
        @if($alerts->count())
            <x-table><thead><tr><th>Granja solar</th><th>Período</th><th>Estimada / real</th><th>Déficit</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
            @foreach($alerts as $alert)
                <tr><td><strong>{{ $alert->solarFarm?->name ?? 'Granja no disponible' }}</strong><span class="block text-[10px] mt-1" style="color:var(--kin-muted)">Incidencia #{{ $alert->id }}</span></td><td>{{ $alert->period }}</td><td><span>{{ number_format($alert->estimated_kwh,1) }}</span> / <strong>{{ number_format($alert->real_kwh,1) }}</strong> kWh</td><td><span class="text-rose-700 dark:text-rose-400 font-semibold">−{{ $alert->deviation_percentage }}%</span></td><td><x-badge :variant="$alert->status === 'active' ? 'danger' : 'success'">{{ $alert->status === 'active' ? 'Activa' : 'Resuelta' }}</x-badge></td><td><x-button :href="route('alerts.show',$alert)" :variant="$alert->status === 'active' ? 'danger' : 'outline'" size="sm" icon="arrow-up-right">{{ $alert->status === 'active' ? 'Ver y atender' : 'Ver resolución' }}</x-button></td></tr>
            @endforeach
            </tbody></x-table>
            <div class="mt-5">{{ $alerts->links() }}</div>
        @else
            <x-empty-state title="Todo en orden" message="No hay alertas que coincidan con los filtros seleccionados." icon="shield-check"/>
        @endif
    </section>
</div>
@endsection
