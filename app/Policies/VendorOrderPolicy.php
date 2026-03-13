<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorOrderPolicy
{
    use HandlesAuthorization;

    public function view(Vendor $vendor, Order $order): bool
    {
        return $vendor->id === $order->vendor_id;
    }

    public function update(Vendor $vendor, Order $order): bool
    {
        return $vendor->id === $order->vendor_id;
    }

    public function viewAny(Vendor $vendor): bool
    {
        return true;
    }
}

class VendorProductPolicy
{
    use HandlesAuthorization;

    public function view(Vendor $vendor, Product $product): bool
    {
        return $vendor->id === $product->vendor_id;
    }

    public function update(Vendor $vendor, Product $product): bool
    {
        return $vendor->id === $product->vendor_id;
    }

    public function delete(Vendor $vendor, Product $product): bool
    {
        return $vendor->id === $product->vendor_id;
    }

    public function create(Vendor $vendor): bool
    {
        return true;
    }
}
