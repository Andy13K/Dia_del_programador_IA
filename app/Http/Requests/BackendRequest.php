<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BackendRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe ser al menos :min.',
            'max' => 'El campo :attribute supera el máximo permitido de :max.',
            'between' => 'El campo :attribute debe estar entre :min y :max.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
            'exists' => 'El registro seleccionado en :attribute no está disponible.',
            'array' => 'El campo :attribute debe ser una lista válida.',
            'distinct' => 'El campo :attribute no puede repetirse.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'date_format' => 'El campo :attribute debe tener el formato :format.',
            'regex' => 'El formato de :attribute no es válido.',
            'decimal' => 'El campo :attribute tiene demasiados decimales.',
            'period.unique' => 'Ya existe una medición para esta granja y período.',
        ];
    }
}
