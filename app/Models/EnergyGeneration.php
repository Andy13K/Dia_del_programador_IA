<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EnergyGeneration extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'solar_farm_id',
        'period',
        'record_date',
        'estimated_kwh',
        'real_kwh',
        'co2_kg',
        'notes',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'estimated_kwh' => 'decimal:2',
            'real_kwh' => 'decimal:2',
            'co2_kg' => 'decimal:2',
        ];
    }

    public function solarFarm(): BelongsTo
    {
        return $this->belongsTo(SolarFarm::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function generationAlert(): HasOne
    {
        return $this->hasOne(GenerationAlert::class);
    }
}
