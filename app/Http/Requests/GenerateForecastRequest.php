<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class GenerateForecastRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-forecasts') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->exists('target_period')) {
            $this->merge(['target_period' => now()->startOfMonth()->addMonth()->format('Y-m')]);
        }
    }

    public function rules(): array
    {
        return [
            'solar_farm_id' => ['sometimes', 'required', 'integer', Rule::exists('solar_farms', 'id')->whereNull('deleted_at')],
            'target_period' => ['required', 'string', 'date_format:Y-m', 'regex:/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D'],
        ];
    }
}
