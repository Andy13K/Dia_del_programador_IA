<?php

declare(strict_types=1);

namespace App\Http\Requests;

class ReportFilterRequest extends BackendRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', 'in:departamental,granjas,ambiental,alertas'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'auto_print' => ['nullable', 'boolean'],
        ];
    }
}
