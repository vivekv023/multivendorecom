<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();

        $products = [
            // TechZone Electronics (Vendor 1)
            [
                'vendor_id' => $vendors[0]->id,
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'Premium noise-cancelling wireless headphones with 30-hour battery life',
                'price' => 149.99,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[0]->id,
                'name' => 'Smart Watch Pro',
                'description' => 'Advanced smartwatch with health monitoring and GPS',
                'price' => 299.99,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[0]->id,
                'name' => 'Portable Power Bank 20000mAh',
                'description' => 'High-capacity portable charger with fast charging support',
                'price' => 49.99,
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[0]->id,
                'name' => '4K Webcam with Microphone',
                'description' => 'Professional streaming webcam with built-in microphone',
                'price' => 89.99,
                'stock' => 45,
                'is_active' => true,
            ],
            // Fashion Forward (Vendor 2)
            [
                'vendor_id' => $vendors[1]->id,
                'name' => 'Designer Leather Jacket',
                'description' => 'Genuine leather jacket with modern fit',
                'price' => 199.99,
                'stock' => 25,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'name' => 'Classic Denim Jeans',
                'description' => 'Premium denim jeans with comfortable fit',
                'price' => 79.99,
                'stock' => 60,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'name' => 'Running Sneakers',
                'description' => 'Lightweight athletic shoes with cushioning',
                'price' => 119.99,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'name' => 'Silk Scarf',
                'description' => '100% pure silk scarf with elegant patterns',
                'price' => 59.99,
                'stock' => 35,
                'is_active' => true,
            ],
            // Home & Living (Vendor 3)
            [
                'vendor_id' => $vendors[2]->id,
                'name' => 'Modern Table Lamp',
                'description' => 'Elegant LED table lamp with adjustable brightness',
                'price' => 45.99,
                'stock' => 55,
                'is_active' => true,
            ],
            [
                'vendor_id' => $vendors[2]->id,
                'name' => 'Coffee Maker Deluxe',
                'description' => 'Programmable coffee maker with thermal carafe',
                'price' => 89.99,
                'stock' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
