<?php
namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'property_id' => Property::factory(),
        ];
    }
}