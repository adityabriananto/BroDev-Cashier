<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $response = $this->get('/admin/products');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_products_list(): void
    {
        $user = User::factory()->create();
        Product::factory()->create();

        $response = $this->actingAs($user)->get('/admin/products');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.products.store'), [
            'sku' => 'SKU-UNIQUE-123',
            'name' => 'Test Product',
            'price' => 5000,
            'stock' => 10,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Product created successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'sku', 'name', 'price', 'stock'],
            ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'SKU-UNIQUE-123',
            'name' => 'Test Product',
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'sku' => 'SKU-OLD',
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($user)->putJson(route('api.products.update', ['id' => $product->id]), [
            'sku' => 'SKU-NEW',
            'name' => 'New Name',
            'price' => 12000,
            'stock' => 20,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Product updated successfully',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => 'SKU-NEW',
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('api.products.destroy', ['id' => $product->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Product deleted successfully',
            ]);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_restore_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $product->delete();

        $response = $this->actingAs($user)->postJson(route('api.products.restore', ['id' => $product->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Product restored successfully',
            ]);

        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_get_transaction_details(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Product A',
            'price' => 15000,
        ]);
        $transaction = Transaction::factory()->create();
        $transaction->products()->attach($product->id, [
            'quantity' => 2,
            'price_at_transaction' => 15000,
        ]);

        $response = $this->actingAs($user)->getJson(route('api.transactions.detail', ['id' => $transaction->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Transaction details retrieved successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['name', 'sku', 'quantity', 'price_at_transaction', 'current_price', 'is_price_changed'],
                ],
            ]);
    }

    public function test_admin_gets_404_for_non_existent_transaction(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('api.transactions.detail', ['id' => 99999]));

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Transaction not found',
            ]);
    }
}
