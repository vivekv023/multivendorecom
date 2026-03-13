<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getAllForAdmin(array $filters = []): LengthAwarePaginator;
    public function getById(int $id): ?Order;
    public function getByVendor(int $vendorId): Collection;
    public function getByUser(int $userId): Collection;
    public function updateStatus(Order $order, string $status): bool;
    public function getStatsForVendor(int $vendorId): array;
}
