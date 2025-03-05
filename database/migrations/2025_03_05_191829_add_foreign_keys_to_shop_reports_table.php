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
        Schema::table('shop_reports', function (Blueprint $table) {
            // Add foreign key constraints
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('shop_id')
                  ->references('id')
                  ->on('shops')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_reports', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['shop_id']);
        });
    }
};
