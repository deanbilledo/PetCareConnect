<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('operating_hours', function (Blueprint $table) {
            $table->boolean('has_lunch_break')->default(false);
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
        });
    }

    public function down()
    {
        Schema::table('operating_hours', function (Blueprint $table) {
            $table->dropColumn(['has_lunch_break', 'lunch_start', 'lunch_end']);
        });
    }
}; 