<?php

namespace App\Actions\Sales;

use App\Models\Product;
use App\Models\Transaction;
use App\Services\InventoryService;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompleteSaleAction
{
    public function __construct(
        protected ProductService $productService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Execute the complete sale checkout process inside a transaction boundary.
     *
     * @throws \Exception
     */
    public function execute(array $cart, string $paymentMethod): Transaction
    {
        return DB::transaction(function () use ($cart, $paymentMethod) {
            // Calculate totals on server side
            $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
            $tax = (int) ($subtotal * 0.11); // 11% tax
            $total = $subtotal + $tax;

            // Create transaction fact
            $transaction = Transaction::create([
                'transaction_code' => 'TXS-'.strtoupper(Str::random(8)),
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            foreach ($cart as $item) {
                // Find and lock product for update to prevent concurrent stock issues
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Decrement stock in product catalog
                $product->decrement('stock', $item['quantity']);

                // Attach to pivot table locking historical price
                $transaction->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $product->price,
                ]);

                // Record stock movement inside inventory
                $this->inventoryService->recordSaleMovement($product->id, $item['quantity'], $transaction->id);
            }

            return $transaction;
        });
    }
}
