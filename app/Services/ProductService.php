<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected CartService $cartService
    ) {}

    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getAllActive($filters);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->getById($id);
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return $this->productRepository->getRelatedProducts($product, $limit);
    }

    public function searchProducts(string $search, array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->search($search, $filters);
    }

    public function getCartCount(): int
    {
        return $this->cartService->getCartItemCount();
    }
}
