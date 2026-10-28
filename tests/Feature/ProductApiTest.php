<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_products(): void
    {
        $currency = Currency::factory()->create(['symbol' => 'USD']);
        Product::factory()->create([
            'name' => 'Notebook',
            'currency_id' => $currency->id,
            'price' => 1200.5,
        ]);

        $response = $this->getJson('/products');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Notebook')
            ->assertJsonPath('data.0.price', '1200.50')
            ->assertJsonPath('data.0.currency.symbol', 'USD');
    }

    public function test_creates_a_product(): void
    {
        $currency = Currency::factory()->create();

        $response = $this->postJson('/products', [
            'name' => 'Mouse',
            'description' => 'Mouse inalámbrico',
            'price' => 25.5,
            'currency_id' => $currency->id,
            'tax_cost' => 4.75,
            'manufacturing_cost' => 8,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Mouse')
            ->assertJsonPath('data.price', '25.50')
            ->assertJsonPath('data.tax_cost', '4.75')
            ->assertJsonPath('data.manufacturing_cost', '8.00')
            ->assertJsonPath('data.currency.id', $currency->id);

        $this->assertDatabaseHas('products', [
            'name' => 'Mouse',
            'currency_id' => $currency->id,
        ]);
    }

    public function test_rejects_an_incomplete_product(): void
    {
        $response = $this->postJson('/products', []);

        $response->assertUnprocessable()
            ->assertJsonPath('errors.name.0', 'El campo nombre es obligatorio.');
    }

    public function test_shows_updates_and_deletes_a_product(): void
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Teclado',
            'currency_id' => $currency->id,
        ]);

        $this->getJson("/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Teclado');

        $this->putJson("/products/{$product->id}", [
            'name' => 'Teclado mecánico',
            'description' => 'Con switches rojos',
            'price' => 80,
            'currency_id' => $currency->id,
            'tax_cost' => 10,
            'manufacturing_cost' => 30,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Teclado mecánico')
            ->assertJsonPath('data.price', '80.00');

        $this->deleteJson("/products/{$product->id}")->assertNoContent();

        $this->getJson("/products/{$product->id}")->assertNotFound();
    }

    public function test_registers_a_price_in_another_currency(): void
    {
        $base = Currency::factory()->create(['symbol' => 'USD']);
        $eur = Currency::factory()->create(['symbol' => 'EUR']);
        $product = Product::factory()->create(['currency_id' => $base->id]);

        $this->getJson("/products/{$product->id}/prices")
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->postJson("/products/{$product->id}/prices", [
            'currency_id' => $eur->id,
            'price' => 22.4,
        ])->assertCreated()
            ->assertJsonPath('data.price', '22.40')
            ->assertJsonPath('data.currency.symbol', 'EUR')
            ->assertJsonPath('data.product_id', $product->id);

        $this->postJson("/products/{$product->id}/prices", [
            'currency_id' => $eur->id,
            'price' => 23,
        ])->assertUnprocessable()
            ->assertJsonPath('errors.currency_id.0', 'Este producto ya tiene un precio en la divisa seleccionada.');
    }

    public function test_filters_sorts_and_paginates_products(): void
    {
        $currency = Currency::factory()->create();
        Product::factory()->create([
            'name' => 'Alpha',
            'price' => 10,
            'currency_id' => $currency->id,
        ]);
        Product::factory()->create([
            'name' => 'Beta',
            'price' => 30,
            'currency_id' => $currency->id,
        ]);

        $this->getJson('/products?filter[name]=alp&sort=-price&perPage=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Alpha')
            ->assertJsonPath('meta.per_page', 1);
    }
}
