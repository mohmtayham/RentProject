<?php
namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(10)->get();

        foreach ($users as $sender) {
            $recipients = $users->where('id', '!=', $sender->id)->random(3);
            
            foreach ($recipients as $recipient) {
                Message::factory()->count(rand(1, 3))->create([
                    'sender_id' => $sender->id,
                    'recipient_id' => $recipient->id,
                ]);
            }
        }

        Message::factory()->count(20)->create();
    }
}