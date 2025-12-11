<?php
namespace Database\Seeders;

use App\Models\Application;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::take(5)->get();
        $properties = Property::take(10)->get();
        $admins = Admin::take(2)->get();

        foreach ($tenants as $tenant) {
            foreach ($properties->random(2) as $property) {
                Application::factory()->create([
                    'tenant_id' => $tenant->id,
                    'property_id' => $property->id,
                    'admin_id' => $admins->random()->id,
                ]);
            }
        }

        Application::factory()->count(15)->create();
    }
}