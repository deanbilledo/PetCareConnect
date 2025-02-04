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
        // The employee_id column and foreign key were already added by a previous migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to do anything since we didn't make any changes
    }
};
