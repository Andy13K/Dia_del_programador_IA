<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSolarFarmRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:150'],
            'latitude' => ['required', 'numeric', 'between:13.0,18.5'],
            'longitude' => ['required', 'numeric', 'between:-93.0,-87.5'],
            'benefited_families' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'status' => ['required', 'in:active,inactive,maintenance'],
            'panels' => ['sometimes', 'array'],
            'panels.*' => ['required', 'array:panel_id,quantity'],
            'panels.*.panel_id' => ['required', 'integer', 'distinct', Rule::exists('solar_panels', 'id')->whereNull('deleted_at')],
            'panels.*.quantity' => ['required', 'integer', 'min:1', 'max:4294967295'],
        ];
    }
}
