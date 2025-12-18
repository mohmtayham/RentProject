<?php

namespace Database\Seeders;

use App\Models\UserWalletTransaction;
use Illuminate\Database\Seeder;

class UserWalletTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Only create wallets for existing tenants
       UserWalletTransaction::factory()->count(10)->create();
    }
}