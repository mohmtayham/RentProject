<?php
namespace Database\Seeders;

use App\Models\Property;
use App\Models\Landlord;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $landlords = Landlord::all();

        foreach ($landlords as $landlord) {
            Property::factory()->count(rand(1, 5))->create([
                'landlord_id' => $landlord->id,
            ]);
        }

        Property::factory()->count(10)->create();
    }
}