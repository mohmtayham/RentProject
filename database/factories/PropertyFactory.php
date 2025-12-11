<?php
namespace Database\Factories;

use App\Models\Landlord;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'landlord_id' => Landlord::factory(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'square_feet' => $this->faker->numberBetween(500, 5000),
            'monthly_rent' => $this->faker->numberBetween(1000, 10000),
            'description' => $this->faker->paragraph(),
            'is_available' => $this->faker->boolean(70),
            'note' => $this->faker->optional()->sentence(),
            'photo' => $this->faker->optional()->imageUrl(),
        ];
    }

    public function available()
    {
        return $this->state([
            'is_available' => true,
        ]);
    }

    public function rented()
    {
        return $this->state([
            'is_available' => false,
        ]);
    }
}