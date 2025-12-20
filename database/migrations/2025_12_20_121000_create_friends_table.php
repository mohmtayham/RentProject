<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('friend_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->boolean('accepted')->default(false);

            $table->timestamps();

            $table->unique(['user_id', 'friend_id']);

            // optional check - may be ignored on some DB drivers
            try {
                $table->check('user_id <> friend_id');
            } catch (\Throwable $e) {
                // Some DB drivers or older MySQL versions may not support check constraints via Blueprint
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
