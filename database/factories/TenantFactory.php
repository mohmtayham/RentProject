<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // create a new user for tenant
            'special_requirements' => $this->faker->optional()->sentence(),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced']),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
        ];
    }
}
