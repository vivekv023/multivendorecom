<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+91 98765 43210',
                'role' => 'customer',
            ],
            [
                'name' => 'Jane Customer',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'phone' => '+91 98765 43211',
                'role' => 'customer',
            ],
            [
                'name' => 'Alex Johnson',
                'email' => 'alex@example.com',
                'password' => Hash::make('password'),
                'phone' => '+91 98765 43212',
                'role' => 'customer',
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'phone' => '+91 98765 43213',
                'role' => 'customer',
            ],
            [
                'name' => 'Mike Brown',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'phone' => '+91 98765 43214',
                'role' => 'customer',
            ],
        ];

        foreach ($customers as $customer) {
            User::create($customer);
        }
    }
}
