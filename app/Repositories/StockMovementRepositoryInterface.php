<?php

namespace App\Repositories;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Collection;

interface StockMovementRepositoryInterface
{
    /**
     * Create a new stock movement.
     */
    public function create(array $data): StockMovement;

    /**
     * Get movements by product ID.
     */
    public function getByProductId(int $productId): Collection;

    /**
     * Get all stock movements.
     */
    public function all(): Collection;
}
