<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(20)->get();
        if ($users->count() < 2) {
            return;
        }

        // Create some random friend requests and accept some
        foreach ($users as $user) {
            $others = $users->where('id', '!=', $user->id)->random(min(3, $users->count() -1));
            foreach ($others as $other) {
                // avoid duplicate
                $exists = DB::table('friends')
                        ->where('user_id', $user->id)
                        ->where('friend_id', $other->id)
                        ->exists();
                if ($exists) continue;

                // insert request
                DB::table('friends')->insert([
                    'user_id' => $user->id,
                    'friend_id' => $other->id,
                    'accepted' => (rand(0,1) === 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
