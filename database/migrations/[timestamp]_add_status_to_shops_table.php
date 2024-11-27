<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending')->after('terms_accepted');
        });

        // Update existing shops to 'active' status
        DB::table('shops')->update(['status' => 'active']);
    }

    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 