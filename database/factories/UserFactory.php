<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),   
            'user_type' => $this->faker->randomElement(['tenant', 'landlord', 'admin']),
            'phone_number' => $this->faker->phoneNumber(),
            'photo' => $this->faker->optional()->imageUrl(),
            'id_photo' => $this->faker->optional()->imageUrl(),
            'address' => $this->faker->optional()->address(),
            'status' => $this->faker->randomElement(['approve', 'reject', 'pending']),


          
        ];
    }

    public function tenant()
    {
        return $this->state([
            'user_type' => 'tenant',
        ]);
    }

    public function landlord()
    {
        return $this->state([
            'user_type' => 'landlord',
        ]);
    }

    public function admin()
    {
        return $this->state([
            'user_type' => 'admin',
        ]);
    }

    public function unverified()
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }
}