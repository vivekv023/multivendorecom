<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'TechZone Electronics',
                'email' => 'techzone@vendor.com',
                'password' => Hash::make('vendor123'),
                'phone' => '+1 555-0101',
                'address' => '123 Tech Street, Silicon Valley, CA 94025',
                'is_active' => true,
            ],
            [
                'name' => 'Fashion Forward',
                'email' => 'fashion@vendor.com',
                'password' => Hash::make('vendor123'),
                'phone' => '+1 555-0102',
                'address' => '456 Fashion Ave, New York, NY 10001',
                'is_active' => true,
            ],
            [
                'name' => 'Home & Living',
                'email' => 'home@vendor.com',
                'password' => Hash::make('vendor123'),
                'phone' => '+1 555-0103',
                'address' => '789 Home Blvd, Chicago, IL 60601',
                'is_active' => true,
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
