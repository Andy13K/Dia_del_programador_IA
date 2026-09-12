<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenerationForecast extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'solar_farm_id',
        'target_period',
        'forecasted_kwh',
        'method',
        'actual_kwh',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'forecasted_kwh' => 'decimal:2',
            'actual_kwh' => 'decimal:2',
        ];
    }

    public function solarFarm(): BelongsTo
    {
        return $this->belongsTo(SolarFarm::class);
    }
}
