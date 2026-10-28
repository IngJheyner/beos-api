<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * currency viaja siempre. prices solo si se pidió con included.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency_id' => $this->currency_id,
            'tax_cost' => $this->tax_cost,
            'manufacturing_cost' => $this->manufacturing_cost,
            'currency' => CurrencyResource::make($this->whenLoaded('currency')),
            'prices' => ProductPriceResource::collection($this->whenLoaded('prices')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
