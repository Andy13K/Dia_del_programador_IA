<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolarFarm extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'department_id',
        'name',
        'latitude',
        'longitude',
        'benefited_families',
        'status',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'benefited_families' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function solarPanels(): BelongsToMany
    {
        return $this->belongsToMany(SolarPanel::class, 'farm_panel')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function energyGenerations(): HasMany
    {
        return $this->hasMany(EnergyGeneration::class);
    }

    public function generationAlerts(): HasMany
    {
        return $this->hasMany(GenerationAlert::class);
    }

    public function generationForecasts(): HasMany
    {
        return $this->hasMany(GenerationForecast::class);
    }

    /**
     * Capacidad instalada total en kW: suma de quantity * nominal_power_kw de cada panel asociado.
     */
    public function getCalculatedCapacityKwAttribute(): float
    {
        return (float) $this->solarPanels
            ->sum(fn (SolarPanel $panel): float => (float) $panel->pivot->quantity * (float) $panel->nominal_power_kw);
    }
}
