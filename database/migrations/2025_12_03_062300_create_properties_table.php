<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->integer('square_feet')->nullable();
            $table->integer('monthly_rent')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('note')->nullable();
            $table->integer('avg_rating')->default(0);
            $table->string('photo')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
    
};
