<?php

declare(strict_types=1);

namespace App\Http\Requests;

class IndexGenerationAlertRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'solar_farm_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:active,resolved'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
