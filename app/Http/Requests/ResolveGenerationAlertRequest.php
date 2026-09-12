<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ResolveGenerationAlertRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['resolution_notes' => ['required', 'string', 'max:500']];
    }
}
