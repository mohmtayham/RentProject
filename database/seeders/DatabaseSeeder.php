<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TenantSeeder::class,
            LandlordSeeder::class,
            AdminSeeder::class,
            PropertySeeder::class,
            ApplicationSeeder::class,
            RentalContractSeeder::class,
            FavoriteSeeder::class,
            MessageSeeder::class,
        ]);
    }
}