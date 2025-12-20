<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('photo');
            }
            if (!Schema::hasColumn('properties', 'longitude')) {
                $table->decimal('longitude', 10, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('properties', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'views')) {
                $table->dropColumn('views');
            }
            if (Schema::hasColumn('properties', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('properties', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};
