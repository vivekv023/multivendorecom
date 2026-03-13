<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getAllActive(array $filters = []): LengthAwarePaginator;
    public function getById(int $id): ?Product;
    public function getByVendor(int $vendorId, int $limit = 4): Collection;
    public function getRelatedProducts(Product $product, int $limit = 4): Collection;
    public function search(string $search, array $filters = []): LengthAwarePaginator;
}
