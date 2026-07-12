<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_index_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_get_products_api_returns_standard_json_success(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->getJson(route('api.products'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'sku', 'name', 'price', 'stock'],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Active products retrieved successfully',
            ]);
    }

    public function test_checkout_processes_successfully(): void
    {
        $product = Product::factory()->create([
            'price' => 10000,
            'stock' => 5,
        ]);

        $response = $this->postJson(route('api.checkout'), [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 2,
                    'price' => 10000,
                ],
            ],
            'payment_method' => 'Cash',
            'total' => 22200, // 20000 + 11% tax (2200) = 22200
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Transaction processed successfully!',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['code'],
            ]);

        $this->assertEquals(3, $product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => -2,
            'type' => 'sale',
            'reason' => 'sale completion',
        ]);
    }

    public function test_checkout_fails_validation_with_standard_error_response(): void
    {
        $response = $this->postJson(route('api.checkout'), [
            'cart' => [],
            'payment_method' => '',
            'total' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'validation failed',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['cart', 'payment_method'],
            ]);
    }

    public function test_checkout_fails_due_to_insufficient_stock_with_business_code(): void
    {
        $product = Product::factory()->create([
            'price' => 10000,
            'stock' => 1,
        ]);

        $response = $this->postJson(route('api.checkout'), [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => 2,
                    'price' => 10000,
                ],
            ],
            'payment_method' => 'Cash',
            'total' => 22200,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'code' => 'insufficient_stock',
            ]);
    }
}
