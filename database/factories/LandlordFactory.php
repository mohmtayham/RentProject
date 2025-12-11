<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandlordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->landlord(),
            'total_properties_managed' => $this->faker->numberBetween(0, 50),
        ];
    }
}
