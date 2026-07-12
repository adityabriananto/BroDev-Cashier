<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {}

    public function getAllProducts(): Collection
    {
        return $this->productRepository->allWithTrashed();
    }

    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActive();
    }

    public function getProductById(int $id): Product
    {
        return $this->productRepository->findById($id);
    }

    public function getProductByIdWithTrashed(int $id): Product
    {
        return $this->productRepository->findByIdWithTrashed($id);
    }

    public function createProduct(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function restoreProduct(int $id): bool
    {
        return $this->productRepository->restore($id);
    }
}
