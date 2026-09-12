<?php

declare(strict_types=1);

namespace App\Http\Requests;

class StoreSolarPanelRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'nominal_power_kw' => ['required', 'numeric', 'min:0.001', 'max:100', 'decimal:0,3'],
            'status' => ['required', 'in:active,inactive,maintenance'],
        ];
    }
}
