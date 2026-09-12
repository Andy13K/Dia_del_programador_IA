<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreEnergyGenerationRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'solar_farm_id' => ['required', 'integer', Rule::exists('solar_farms', 'id')->whereNull('deleted_at')],
            'period' => ['required', 'regex:/^\\d{4}-\\d{2}$/', 'date_format:Y-m',
                Rule::unique('energy_generations', 'period')->where('solar_farm_id', $this->integer('solar_farm_id'))],
            'record_date' => ['required', 'date'],
            'estimated_kwh' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'real_kwh' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return ['period.unique' => 'Ya existe una medición para esta granja y período.'];
    }
}
