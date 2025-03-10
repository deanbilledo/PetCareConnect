<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->enum('status', ['alive', 'deceased'])->default('alive');
            $table->date('death_date')->nullable();
            $table->text('death_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['status', 'death_date', 'death_reason']);
        });
    }
}; 