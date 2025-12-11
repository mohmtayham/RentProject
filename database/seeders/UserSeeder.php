<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

       // Create one known admin for easy login while developing
       User::factory()->admin()->create([
          'name' => 'Super Admin',
          'email' => 'admin@example.com',
          'password' => Hash::make('password'),
       ]);

       // Create additional users by role so related seeders can attach reliably
       User::factory()->count(2)->admin()->create();
       User::factory()->count(10)->landlord()->create();
       User::factory()->count(30)->tenant()->create();

       // Some generic users
       User::factory()->count(10)->create();
      
    }
}