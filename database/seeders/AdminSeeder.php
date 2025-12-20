<?php
namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
          $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('1234'),
            'user_type' => 'admin',
            'address' => 'Admin Address',
            'phone_number' => '+1234567890',
            'status' => 'approve', 
            'email_verified_at' => now(),
        ]);

        // إنشاء سجل في جدول admins
        $admin->admin()->create([
            'department' => 'Administration',
            'approved_at' => now(),
        ]);
        
    }
}