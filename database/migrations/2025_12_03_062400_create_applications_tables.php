<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
    $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
    $table->foreignId('landlord_id')->nullable()->constrained('landlords')->nullOnDelete();
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('monthly_rent', 10, 2);
    $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
    $table->timestamp('submitted_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
