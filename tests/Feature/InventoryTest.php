<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_inventory_routes(): void
    {
        $response = $this->postJson(route('api.inventory.adjust'), [
            'product_id' => 1,
            'quantity' => 5,
            'reason' => 'Restocking',
        ]);
        $response->assertStatus(401);

        $response2 = $this->getJson(route('api.inventory.low-stock'));
        $response2->assertStatus(401);
    }

    public function test_admin_can_adjust_stock_up(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)->postJson(route('api.inventory.adjust'), [
            'product_id' => $product->id,
            'quantity' => 5,
            'reason' => 'Restocking',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Stock adjusted successfully',
                'data' => [
                    'id' => $product->id,
                    'stock' => 15,
                ],
            ]);

        $this->assertEquals(15, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => 5,
            'type' => 'adjustment',
            'reason' => 'Restocking',
        ]);
    }

    public function test_admin_can_adjust_stock_down(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)->postJson(route('api.inventory.adjust'), [
            'product_id' => $product->id,
            'quantity' => -3,
            'reason' => 'Damaged goods',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Stock adjusted successfully',
                'data' => [
                    'id' => $product->id,
                    'stock' => 7,
                ],
            ]);

        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => -3,
            'type' => 'adjustment',
            'reason' => 'Damaged goods',
        ]);
    }

    public function test_adjust_stock_fails_validation_for_zero_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)->postJson(route('api.inventory.adjust'), [
            'product_id' => $product->id,
            'quantity' => 0,
            'reason' => 'No change',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'validation failed',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['quantity'],
            ]);
    }

    public function test_adjust_stock_fails_when_new_stock_is_below_zero(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)->postJson(route('api.inventory.adjust'), [
            'product_id' => $product->id,
            'quantity' => -15,
            'reason' => 'Massive damage',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Stock cannot be adjusted below zero',
            ]);
    }

    public function test_get_low_stock_products(): void
    {
        $user = User::factory()->create();
        $productLow = Product::factory()->create(['stock' => 5]);
        $productNormal = Product::factory()->create(['stock' => 15]);

        $response = $this->actingAs($user)->getJson(route('api.inventory.low-stock'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Low stock products retrieved successfully',
            ])
            ->assertJsonCount(1, 'data');
    }
}
