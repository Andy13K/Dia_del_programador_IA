<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationAlert extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'solar_farm_id',
        'energy_generation_id',
        'period',
        'estimated_kwh',
        'real_kwh',
        'deviation_percentage',
        'status',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_kwh' => 'decimal:2',
            'real_kwh' => 'decimal:2',
            'deviation_percentage' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    public function solarFarm(): BelongsTo
    {
        return $this->belongsTo(SolarFarm::class);
    }

    public function energyGeneration(): BelongsTo
    {
        return $this->belongsTo(EnergyGeneration::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
