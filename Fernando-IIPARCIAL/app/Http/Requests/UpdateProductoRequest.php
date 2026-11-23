<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProductoRequest extends StoreProductoRequest
{
    public function rules(): array
    {
        $reglas = parent::rules();
        unset($reglas['id']);
        $reglas['codigo'] = ['required', 'string', 'max:10', Rule::unique('productos', 'codigo')->ignore($this->route('producto'))];

        return $reglas;
    }
}
