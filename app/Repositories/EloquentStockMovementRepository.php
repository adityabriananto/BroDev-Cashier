<?php

namespace App\Repositories;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Collection;

class EloquentStockMovementRepository implements StockMovementRepositoryInterface
{
    public function create(array $data): StockMovement
    {
        return StockMovement::create($data);
    }

    public function getByProductId(int $productId): Collection
    {
        return StockMovement::where('product_id', $productId)->orderBy('created_at', 'desc')->get();
    }

    public function all(): Collection
    {
        return StockMovement::orderBy('created_at', 'desc')->get();
    }
}
