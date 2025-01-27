<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // First rename the breed_specific column to exotic_pet_service
            $table->renameColumn('breed_specific', 'exotic_pet_service');
            
            // Then add the new exotic_pet_species column
            $table->json('exotic_pet_species')->nullable()->after('exotic_pet_service');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop the new column first
            $table->dropColumn('exotic_pet_species');
            
            // Then rename back to original
            $table->renameColumn('exotic_pet_service', 'breed_specific');
        });
    }
}; 