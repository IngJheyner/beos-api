<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'manufacturing_cost' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.max' => 'El campo nombre no puede superar los 255 caracteres.',
            'description.required' => 'El campo descripción es obligatorio.',
            'price.required' => 'El campo precio es obligatorio.',
            'price.numeric' => 'El campo precio debe ser numérico.',
            'price.min' => 'El campo precio no puede ser negativo.',
            'price.decimal' => 'El campo precio admite como máximo dos decimales.',
            'currency_id.required' => 'El campo divisa es obligatorio.',
            'currency_id.exists' => 'La divisa seleccionada no existe.',
            'tax_cost.required' => 'El campo costo de impuestos es obligatorio.',
            'tax_cost.numeric' => 'El campo costo de impuestos debe ser numérico.',
            'tax_cost.min' => 'El campo costo de impuestos no puede ser negativo.',
            'tax_cost.decimal' => 'El campo costo de impuestos admite como máximo dos decimales.',
            'manufacturing_cost.required' => 'El campo costo de fabricación es obligatorio.',
            'manufacturing_cost.numeric' => 'El campo costo de fabricación debe ser numérico.',
            'manufacturing_cost.min' => 'El campo costo de fabricación no puede ser negativo.',
            'manufacturing_cost.decimal' => 'El campo costo de fabricación admite como máximo dos decimales.',
        ];
    }
}
