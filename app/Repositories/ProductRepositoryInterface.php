<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get all products including soft-deleted ones.
     */
    public function allWithTrashed(): Collection;

    /**
     * Get active sellable products (stock > 0).
     */
    public function getActive(): Collection;

    /**
     * Find a product by ID.
     */
    public function findById(int $id): Product;

    /**
     * Find a product by ID with trashed.
     */
    public function findByIdWithTrashed(int $id): Product;

    /**
     * Create a new product.
     */
    public function create(array $data): Product;

    /**
     * Update an existing product.
     */
    public function update(int $id, array $data): Product;

    /**
     * Delete a product (soft delete).
     */
    public function delete(int $id): bool;

    /**
     * Restore a soft-deleted product.
     */
    public function restore(int $id): bool;
}
