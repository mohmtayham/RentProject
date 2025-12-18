<?php

namespace Database\Seeders;

use App\Models\Userwallet;
use Illuminate\Database\Seeder;

class UserWalletSeeder extends Seeder
{
    public function run(): void
    {
        // Only create wallets for existing tenants
       Userwallet::factory()->count(10)->create();
    }
}