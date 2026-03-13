<?php

namespace App\Repositories\Interfaces;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;

interface VendorRepositoryInterface
{
    public function getById(int $id): ?Vendor;
    public function getByEmail(string $email): ?Vendor;
    public function getAll(): Collection;
    public function getActive(): Collection;
}
