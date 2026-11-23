<?php

namespace App\Http\Requests;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:255', Rule::unique('productos', 'id')],
            'codigo' => ['required', 'string', 'max:255', Rule::unique('productos', 'codigo')],
            'nombre' => ['required', 'string', 'max:255'],
            'marca' => ['required', 'string', 'max:255'],
            'fecha_adquisicion' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'estado' => ['required', Rule::in(Producto::ESTADOS)],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar los :max caracteres.',
            'unique' => 'El :attribute ya esta registrado.',
            'fecha_adquisicion.date_format' => 'La fecha de adquisicion debe ser una fecha valida (AAAA-MM-DD).',
            'fecha_adquisicion.before_or_equal' => 'La fecha de adquisicion no puede ser futura.',
            'estado.in' => 'El estado debe ser disponible, asignado o mantenimiento.',
        ];
    }
}
