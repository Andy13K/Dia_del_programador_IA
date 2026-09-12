<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolarPanel extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'brand',
        'model',
        'nominal_power_kw',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal_power_kw' => 'decimal:3',
        ];
    }

    public function solarFarms(): BelongsToMany
    {
        return $this->belongsToMany(SolarFarm::class, 'farm_panel')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
