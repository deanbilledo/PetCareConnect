<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update existing vaccinations table
        if (Schema::hasTable('pet_vaccinations')) {
            Schema::table('pet_vaccinations', function (Blueprint $table) {
                // First, add the new columns
                $table->string('administered_by')->after('vaccine_name')->nullable();
                $table->date('administered_date')->after('administered_by')->nullable();
                
                // Copy data from old columns to new ones
                DB::statement('UPDATE pet_vaccinations SET administered_by = veterinarian, administered_date = date');
                
                // Then drop the old columns
                $table->dropColumn(['veterinarian', 'date']);
                
                // Make the new columns required
                DB::statement('ALTER TABLE pet_vaccinations MODIFY administered_by VARCHAR(255) NOT NULL');
                DB::statement('ALTER TABLE pet_vaccinations MODIFY administered_date DATE NOT NULL');
            });
        } else {
            // Create the vaccinations table if it doesn't exist
            Schema::create('pet_vaccinations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
                $table->string('vaccine_name');
                $table->string('administered_by');
                $table->date('administered_date');
                $table->date('next_due_date');
                $table->timestamps();
            });
        }

        // Create parasite control records table if it doesn't exist
        if (!Schema::hasTable('pet_parasite_controls')) {
            Schema::create('pet_parasite_controls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
                $table->string('treatment_name');
                $table->enum('treatment_type', ['Flea', 'Tick', 'Worm', 'Other']);
                $table->date('treatment_date');
                $table->date('next_treatment_date');
                $table->timestamps();
            });
        }

        // Create health issues table if it doesn't exist
        if (!Schema::hasTable('pet_health_issues')) {
            Schema::create('pet_health_issues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
                $table->string('issue_title');
                $table->date('identified_date');
                $table->text('description');
                $table->string('treatment');
                $table->text('vet_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('pet_vaccinations')) {
            Schema::table('pet_vaccinations', function (Blueprint $table) {
                // Add back the old columns
                $table->string('veterinarian')->after('vaccine_name')->nullable();
                $table->date('date')->after('veterinarian')->nullable();
                
                // Copy data back
                DB::statement('UPDATE pet_vaccinations SET veterinarian = administered_by, date = administered_date');
                
                // Drop the new columns
                $table->dropColumn(['administered_by', 'administered_date']);
                
                // Make the old columns required
                DB::statement('ALTER TABLE pet_vaccinations MODIFY veterinarian VARCHAR(255) NOT NULL');
                DB::statement('ALTER TABLE pet_vaccinations MODIFY date DATE NOT NULL');
            });
        }
        
        Schema::dropIfExists('pet_health_issues');
        Schema::dropIfExists('pet_parasite_controls');
    }
}; 