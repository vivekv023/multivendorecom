<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllActive(array $filters = []): LengthAwarePaginator
    {
        $query = Product::with('vendor')->where('is_active', true);

        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 12);
    }

    public function getById(int $id): ?Product
    {
        return Product::with('vendor')->find($id);
    }

    public function getByVendor(int $vendorId, int $limit = 4): Collection
    {
        return Product::where('vendor_id', $vendorId)
            ->where('is_active', true)
            ->take($limit)
            ->get();
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return Product::where('vendor_id', $product->vendor_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take($limit)
            ->get();
    }

    public function search(string $search, array $filters = []): LengthAwarePaginator
    {
        return $this->getAllActive(array_merge($filters, ['search' => $search]));
    }
}
