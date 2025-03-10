<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Only rename if the old column exists and new one doesn't
            if (Schema::hasColumn('services', 'breed_specific') && !Schema::hasColumn('services', 'exotic_pet_service')) {
                $table->renameColumn('breed_specific', 'exotic_pet_service');
            }
            
            // Add exotic_pet_species if it doesn't exist
            if (!Schema::hasColumn('services', 'exotic_pet_species')) {
                $table->json('exotic_pet_species')->nullable()->after('exotic_pet_service');
            }
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'exotic_pet_service') && !Schema::hasColumn('services', 'breed_specific')) {
                $table->renameColumn('exotic_pet_service', 'breed_specific');
            }
            if (Schema::hasColumn('services', 'exotic_pet_species')) {
                $table->dropColumn('exotic_pet_species');
            }
        });
    }
}; 