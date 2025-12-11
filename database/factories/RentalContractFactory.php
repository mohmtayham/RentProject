<?php
namespace Database\Factories;

use App\Models\Application;
use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalContractFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 month');
        
        return [
            'application_id' => Application::factory(),
            'property_id' => Property::factory(),
            'tenant_id' => Tenant::factory(),
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, '+1 year'),
            'monthly_rent' => $this->faker->numberBetween(1000, 5000),
             'rate' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['draft', 'active', 'expired', 'terminated']),
            'signed_at' => $this->faker->optional()->dateTimeBetween($startDate, '+1 month'),
        ];
    }

    public function active()
    {
        return $this->state([
            'status' => 'active',
            'signed_at' => now(),
        ]);
    }
}