<?php

namespace App\Models;

use Database\Factories\CurrencyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'symbol', 'exchange_rate'])]
class Currency extends Model
{
    /** @use HasFactory<CurrencyFactory> */
    use HasFactory;

    /**
     * La tasa se guarda con seis decimales para no perder precisión al convertir.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:6',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}
