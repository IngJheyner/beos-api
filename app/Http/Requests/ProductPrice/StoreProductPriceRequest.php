<?php

namespace App\Http\Requests\ProductPrice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Un producto solo puede tener un precio por divisa.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::unique('product_prices', 'currency_id')->where('product_id', $productId),
            ],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'currency_id.required' => 'El campo divisa es obligatorio.',
            'currency_id.exists' => 'La divisa seleccionada no existe.',
            'currency_id.unique' => 'Este producto ya tiene un precio en la divisa seleccionada.',
            'price.required' => 'El campo precio es obligatorio.',
            'price.numeric' => 'El campo precio debe ser numérico.',
            'price.min' => 'El campo precio no puede ser negativo.',
            'price.decimal' => 'El campo precio admite como máximo dos decimales.',
        ];
    }
}
