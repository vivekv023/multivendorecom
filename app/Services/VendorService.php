<?php

namespace App\Services;

use App\Models\Vendor;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VendorService
{
    public function __construct(
        protected VendorRepositoryInterface $vendorRepository
    ) {}

    public function getById(int $id): ?Vendor
    {
        return $this->vendorRepository->getById($id);
    }

    public function getByEmail(string $email): ?Vendor
    {
        return $this->vendorRepository->getByEmail($email);
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->vendorRepository->getAll();
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->vendorRepository->getActive();
    }

    public function authenticate(string $email, string $password): Vendor
    {
        $vendor = $this->vendorRepository->getByEmail($email);

        if (!$vendor) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$vendor->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if (!Hash::check($password, $vendor->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $vendor;
    }

    public function findOrderForVendor(Vendor $vendor, int $orderId): ?\App\Models\Order
    {
        return $vendor->orders()->find($orderId);
    }
}
