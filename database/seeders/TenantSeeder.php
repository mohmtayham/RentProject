<?php
namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenantUsers = User::where('user_type', 'tenant')->get();

        foreach ($tenantUsers as $user) {
            Tenant::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        Tenant::factory()->count(5)->create();
    }
}