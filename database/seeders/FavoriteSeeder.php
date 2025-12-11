<?php
namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::take(5)->get();
        $properties = Property::take(10)->get();

        foreach ($tenants as $tenant) {
            foreach ($properties->random(3) as $property) {
                Favorite::factory()->create([
                    'tenant_id' => $tenant->id,
                    'property_id' => $property->id,
                ]);
            }
        }

        Favorite::factory()->count(10)->create();
    }
}