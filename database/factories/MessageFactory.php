<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'recipient_id' => User::factory(),
            'body' => $this->faker->paragraph(),
            'read_at' => $this->faker->optional()->dateTime(),
        ];
    }

    public function unread()
    {
        return $this->state([
            'read_at' => null,
        ]);
    }
}