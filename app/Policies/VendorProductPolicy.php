<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

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
