<?php
namespace Database\Factories;

use App\Models\Application;
use App\Models\Landlord;
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