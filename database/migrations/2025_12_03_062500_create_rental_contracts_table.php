<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('rental_contracts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('application_id')->constrained()->cascadeOnDelete();
    $table->foreignId('property_id')->constrained()->cascadeOnDelete();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('monthly_rent', 10, 2);
    $table->integer('rate');
    $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
    $table->timestamp('signed_at')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_contracts');
    }
};
