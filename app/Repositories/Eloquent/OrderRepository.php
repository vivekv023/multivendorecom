<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllForAdmin(array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['user', 'vendor', 'payment']);

        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('order_number', 'like', "%{$filters['search']}%");
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getById(int $id): ?Order
    {
        return Order::with(['user', 'vendor', 'items.product', 'payment'])->find($id);
    }

    public function getByVendor(int $vendorId): Collection
    {
        return Order::where('vendor_id', $vendorId)
            ->with(['user', 'payment', 'items.product'])
            ->latest()
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(['vendor', 'items.product', 'payment'])
            ->latest()
            ->get();
    }

    public function updateStatus(Order $order, string $status): bool
    {
        return $order->update(['status' => $status]);
    }

    public function getStatsForVendor(int $vendorId): array
    {
        $orders = Order::where('vendor_id', $vendorId);

        return [
            'total_orders' => (clone $orders)->count(),
            'pending_orders' => (clone $orders)->where('status', 'pending')->count(),
            'processing_orders' => (clone $orders)->where('status', 'processing')->count(),
            'delivered_orders' => (clone $orders)->where('status', 'delivered')->count(),
            'total_revenue' => (clone $orders)->where('status', '!=', 'cancelled')->sum('total_amount'),
        ];
    }
}
