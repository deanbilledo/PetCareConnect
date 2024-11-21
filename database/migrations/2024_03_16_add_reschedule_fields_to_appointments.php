<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->text('reschedule_reason')->nullable()->after('cancellation_reason');
            $table->timestamp('last_reschedule_at')->nullable()->after('reschedule_reason');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['reschedule_reason', 'last_reschedule_at']);
        });
    }
}; 