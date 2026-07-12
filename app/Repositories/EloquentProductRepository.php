<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function allWithTrashed(): Collection
    {
        return Product::withTrashed()->orderBy('created_at', 'desc')->get();
    }

    public function getActive(): Collection
    {
        return Product::where('stock', '>', 0)->get();
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function findByIdWithTrashed(int $id): Product
    {
        return Product::withTrashed()->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);

        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->findById($id);

        return $product->delete();
    }

    public function restore(int $id): bool
    {
        $product = $this->findByIdWithTrashed($id);

        return $product->restore();
    }
}
