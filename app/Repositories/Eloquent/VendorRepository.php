<?php

namespace App\Repositories\Eloquent;

use App\Models\Vendor;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VendorRepository implements VendorRepositoryInterface
{
    public function getById(int $id): ?Vendor
    {
        return Vendor::find($id);
    }

    public function getByEmail(string $email): ?Vendor
    {
        return Vendor::where('email', $email)->first();
    }

    public function getAll(): Collection
    {
        return Vendor::orderBy('name')->get();
    }

    public function getActive(): Collection
    {
        return Vendor::where('is_active', true)->orderBy('name')->get();
    }
}
