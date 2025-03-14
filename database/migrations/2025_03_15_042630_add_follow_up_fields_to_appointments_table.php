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
            $table->boolean('is_follow_up')->default(false)->after('notes');
            $table->unsignedBigInteger('follow_up_for')->nullable()->after('is_follow_up');
            $table->foreign('follow_up_for')->references('id')->on('appointments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['follow_up_for']);
            $table->dropColumn(['is_follow_up', 'follow_up_for']);
        });
    }
};
