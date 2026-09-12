<?php

namespace App\Providers;

use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use App\Policies\EnergyGenerationPolicy;
use App\Policies\GenerationAlertPolicy;
use App\Policies\SolarFarmPolicy;
use App\Policies\SolarPanelPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(SolarFarm::class, SolarFarmPolicy::class);
        Gate::policy(SolarPanel::class, SolarPanelPolicy::class);
        Gate::policy(EnergyGeneration::class, EnergyGenerationPolicy::class);
        Gate::policy(GenerationAlert::class, GenerationAlertPolicy::class);

        // Abilities de nivel de módulo usadas por routes/web.php (docs/06-CONTRATOS-HORA-1.md §2)
        // mientras los controladores reales (Agente B) adoptan las Policies por objeto de arriba.
        Gate::define('manage-farms', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador']));
        Gate::define('manage-panels', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador']));
        Gate::define('manage-generations', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador']));
        Gate::define('manage-alerts', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador']));
        Gate::define('manage-forecasts', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador']));

        // Resto de la matriz de permisos por rol (docs/06-CONTRATOS-HORA-1.md §5). manage-users
        // y view-reports ya los usa BackendAccessService::farms() (Agente B, PR #2) para decidir
        // si un usuario ve el listado nacional de granjas o solo las suyas: sin estos Gates
        // registrados, Gate::allows() siempre niega y todo el mundo quedaba limitado a "sus"
        // granjas al leer, incluido el rol visualizador que según el contrato ve todo.
        Gate::define('manage-users', fn (User $user): bool => $user->hasRole('admin'));
        Gate::define('manage-departments', fn (User $user): bool => $user->hasRole('admin'));
        Gate::define('view-reports', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador', 'visualizador']));
        Gate::define('view-api', fn (User $user): bool => $user->hasAnyRole(['admin', 'operador', 'visualizador']));
    }
}
