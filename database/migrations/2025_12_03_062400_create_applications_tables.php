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
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->foreignId('property_id')->constrained()->cascadeOnDelete();
    $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
    $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
    $table->timestamp('submitted_at')->useCurrent();
    $table->text('notes')->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
