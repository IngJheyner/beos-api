<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Divisas de ejemplo para poder crear productos sin un CRUD de divisas.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'symbol' => 'USD', 'exchange_rate' => 1],
            ['name' => 'Euro', 'symbol' => 'EUR', 'exchange_rate' => 0.92],
            ['name' => 'Colombian Peso', 'symbol' => 'COP', 'exchange_rate' => 4100],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->updateOrCreate(
                ['symbol' => $currency['symbol']],
                $currency,
            );
        }
    }
}
