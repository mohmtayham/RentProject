<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->admin(),
            'department' => $this->faker->randomElement(['Support', 'Finance', 'Operations', 'Management']),
            'approved_at' => now(),
        ];
    }

}
