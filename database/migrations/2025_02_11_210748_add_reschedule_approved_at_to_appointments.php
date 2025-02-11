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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'reschedule_approved_at')) {
                $table->timestamp('reschedule_approved_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'requested_date')) {
                $table->timestamp('requested_date')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'last_reschedule_at')) {
                $table->timestamp('last_reschedule_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'reschedule_reason')) {
                $table->string('reschedule_reason')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'reschedule_rejection_reason')) {
                $table->string('reschedule_rejection_reason')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'reschedule_rejected_at')) {
                $table->timestamp('reschedule_rejected_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $columns = [
                'reschedule_approved_at',
                'requested_date',
                'last_reschedule_at',
                'reschedule_reason',
                'reschedule_rejection_reason',
                'reschedule_rejected_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('appointments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
