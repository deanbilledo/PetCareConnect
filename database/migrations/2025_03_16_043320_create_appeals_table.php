<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->morphs('appealable'); // Polymorphic relationship to connect with both shop reports and user reports
            $table->text('reason'); // The reason for the appeal
            $table->string('evidence_path')->nullable(); // Path to any evidence file
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // Notes from admin reviewing the appeal
            $table->timestamp('resolved_at')->nullable(); // When the appeal was resolved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeals');
    }
};
