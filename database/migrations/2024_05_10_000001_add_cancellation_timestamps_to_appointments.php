<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'cancellation_requested_at')) {
                $table->timestamp('cancellation_requested_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'cancellation_approved_at')) {
                $table->timestamp('cancellation_approved_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'cancellation_rejected_at')) {
                $table->timestamp('cancellation_rejected_at')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'cancellation_rejection_reason')) {
                $table->text('cancellation_rejection_reason')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_requested_at',
                'cancellation_approved_at',
                'cancellation_rejected_at',
                'cancellation_rejection_reason'
            ]);
        });
    }
}; 