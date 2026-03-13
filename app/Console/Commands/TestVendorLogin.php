<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestVendorLogin extends Command
{
    protected $signature = 'app:test-vendor-login';
    protected $description = 'Test vendor login';

    public function handle()
    {
        $vendor = Vendor::where('email', 'techzone@vendor.com')->first();
        
        if (!$vendor) {
            $this->error('Vendor not found');
            return 0;
        }
        
        $this->info("Vendor: {$vendor->email}");
        $this->info("Is active: " . ($vendor->is_active ? 'yes' : 'no'));
        
        $check = Hash::check('password', $vendor->password);
        $this->info("Password check: " . ($check ? 'TRUE' : 'FALSE'));
        
        return 0;
    }
}
