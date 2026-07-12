<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\StockMovementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected StockMovementRepositoryInterface $stockMovementRepository
    ) {}

    /**
     * Adjust stock of a product manually.
     */
    public function adjustStock(int $productId, int $quantityChange, string $reason): Product
    {
        return DB::transaction(function () use ($productId, $quantityChange, $reason) {
            $product = $this->productRepository->findById($productId);
            $newStock = $product->stock + $quantityChange;

            if ($newStock < 0) {
                throw new \Exception('Stock cannot be adjusted below zero');
            }

            // Update product stock (maintain backward compatibility)
            $product->update(['stock' => $newStock]);

            // Record stock movement
            $this->stockMovementRepository->create([
                'product_id' => $product->id,
                'quantity' => $quantityChange,
                'type' => 'adjustment',
                'reason' => $reason,
            ]);

            return $product;
        });
    }

    /**
     * Record stock movement for a transaction (checkout/sale).
     */
    public function recordSaleMovement(int $productId, int $quantityReduced, int $transactionId): Product
    {
        $product = $this->productRepository->findById($productId);

        $this->stockMovementRepository->create([
            'product_id' => $product->id,
            'quantity' => -$quantityReduced,
            'type' => 'sale',
            'reason' => 'sale completion',
            'transaction_id' => $transactionId,
        ]);

        return $product;
    }

    /**
     * Get products with low stock.
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return Product::where('stock', '<', $threshold)->get();
    }
}
