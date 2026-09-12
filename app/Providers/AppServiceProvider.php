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
    }
}
