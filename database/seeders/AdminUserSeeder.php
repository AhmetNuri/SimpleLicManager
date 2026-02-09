<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\License;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create a demo user
        $demo = User::create([
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create sample licenses for demo user
        License::create([
            'user_id' => $demo->id,
            'serial_number' => 'DEMO-1234-5678-ABCD',
            'product_package' => 'ModalMasterPro',
            'license_type' => 'yearly',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'user_enable' => true,
            'emergency' => false,
            'max_connection_count' => 1,
        ]);

        License::create([
            'user_id' => $demo->id,
            'serial_number' => 'DEMO-ABCD-1234-EFGH',
            'product_package' => 'BasicPackage',
            'license_type' => 'lifetime',
            'starts_at' => now(),
            'expires_at' => null,
            'user_enable' => true,
            'emergency' => false,
            'max_connection_count' => 1,
        ]);

        $this->command->info('Admin user created: admin@example.com / password');
        $this->command->info('Demo user created: demo@example.com / password');
        $this->command->info('Sample licenses created for demo user.');
    }
}
