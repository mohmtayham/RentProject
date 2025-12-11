<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->tenant(),
            'special_requirements' => $this->faker->optional()->sentence(),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced']),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
        ];
    }
}