<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Vendor;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function getAllForAdmin(array $filters = []): LengthAwarePaginator
    {
        return $this->orderRepository->getAllForAdmin($filters);
    }

    public function getById(int $id): ?Order
    {
        return $this->orderRepository->getById($id);
    }

    public function getByVendor(int $vendorId): Collection
    {
        return $this->orderRepository->getByVendor($vendorId);
    }

    public function getByUser(int $userId): Collection
    {
        return $this->orderRepository->getByUser($userId);
    }

    public function updateStatus(Order $order, string $status): bool
    {
        return $this->orderRepository->updateStatus($order, $status);
    }

    public function getStatsForVendor(int $vendorId): array
    {
        return $this->orderRepository->getStatsForVendor($vendorId);
    }

    public function getStatsForVendorModel(Vendor $vendor): array
    {
        return $this->orderRepository->getStatsForVendor($vendor->id);
    }

    public function getVendorOrdersWithRelations(Vendor $vendor, int $perPage = 10): LengthAwarePaginator
    {
        return $vendor->orders()
            ->with(['user', 'payment', 'items', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }
}
