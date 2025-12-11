<?php
namespace Database\Seeders;

use App\Models\Landlord;
use App\Models\User;
use Illuminate\Database\Seeder;

class LandlordSeeder extends Seeder
{
    public function run(): void
    {
        $landlordUsers = User::where('user_type', 'landlord')->get();

        foreach ($landlordUsers as $user) {
            Landlord::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        Landlord::factory()->count(3)->create();
    }
}