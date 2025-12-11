<?php
namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsers = User::where('user_type', 'admin')->get();

        foreach ($adminUsers as $user) {
            Admin::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        Admin::factory()->count(2)->create();
    }
}