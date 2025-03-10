<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('employees', 'rating')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->decimal('rating', 3, 2)->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
}; 