<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to alter the column type to avoid requiring doctrine/dbal
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `properties` MODIFY `avg_rating` DECIMAL(3,2) NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `properties` MODIFY `avg_rating` INT NOT NULL DEFAULT 0");
    }
};
