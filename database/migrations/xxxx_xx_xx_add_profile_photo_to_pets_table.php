<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pets', function (Blueprint $table) {
            if (!Schema::hasColumn('pets', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            if (Schema::hasColumn('pets', 'profile_photo_path')) {
                $table->dropColumn('profile_photo_path');
            }
        });
    }
}; 