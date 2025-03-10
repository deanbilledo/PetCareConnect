<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('appointments', 'has_rating')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->boolean('has_rating')->default(false);
            });
        }
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('has_rating');
        });
    }
}; 