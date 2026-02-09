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
        // Create default admin user (admin@root.com / 123123)
        $admin = User::create([
            'email' => 'admin@root.com',
            'password' => Hash::make('123123'),
            'role' => 'admin',
            'name_surname' => 'System Admin',
            'company' => 'SimpleLicManager',
        ]);

        // Create demo user
        $demo = User::create([
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'name_surname' => 'Demo User',
            'company' => 'Demo Company',
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

        $this->command->info('Admin user created: admin@root.com / 123123');
        $this->command->info('Demo user created: demo@example.com / password');
        $this->command->info('Sample licenses created for demo user.');
    }
}
