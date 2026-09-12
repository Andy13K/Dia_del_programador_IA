<?php

declare(strict_types=1);

namespace App\Http\Requests;

class IndexEnergyGenerationRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'solar_farm_id' => ['nullable', 'integer'],
            'period' => ['nullable', 'date_format:Y-m'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
