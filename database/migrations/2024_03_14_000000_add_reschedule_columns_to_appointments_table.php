<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status')->default('pending')->change(); // Make sure status can handle new states
            $table->dateTime('requested_date')->nullable();
            $table->string('requested_service')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->text('reschedule_rejection_reason')->nullable();
            $table->dateTime('last_reschedule_at')->nullable();
            $table->dateTime('reschedule_approved_at')->nullable();
            $table->dateTime('reschedule_rejected_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'requested_date',
                'requested_service',
                'reschedule_reason',
                'reschedule_rejection_reason',
                'last_reschedule_at',
                'reschedule_approved_at',
                'reschedule_rejected_at'
            ]);
        });
    }
}; 