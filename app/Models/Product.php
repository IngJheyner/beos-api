<?php

namespace App\Models;

use App\Traits\ApiTrait;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use ApiTrait, HasFactory;

    /** @var list<string> */
    protected $allowIncluded = ['currency', 'prices', 'prices.currency'];

    /** @var list<string> */
    protected $allowFilter = ['id', 'name', 'description'];

    /** @var list<string> */
    protected $allowSort = ['id', 'name', 'price', 'tax_cost', 'manufacturing_cost'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'tax_cost' => 'decimal:2',
            'manufacturing_cost' => 'decimal:2',
        ];
    }

    /**
     * Divisa base del precio guardado en el producto.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Precios del mismo producto en otras divisas.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }
}
