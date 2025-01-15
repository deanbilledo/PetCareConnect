<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Remove old price column if it exists
            if (Schema::hasColumn('services', 'price')) {
                $table->dropColumn('price');
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('services', 'category')) {
                $table->string('category')->after('name');
            }
            if (!Schema::hasColumn('services', 'pet_types')) {
                $table->json('pet_types')->after('description');
            }
            if (!Schema::hasColumn('services', 'size_ranges')) {
                $table->json('size_ranges')->after('pet_types');
            }
            if (!Schema::hasColumn('services', 'breed_specific')) {
                $table->boolean('breed_specific')->default(false)->after('size_ranges');
            }
            if (!Schema::hasColumn('services', 'special_requirements')) {
                $table->text('special_requirements')->nullable()->after('breed_specific');
            }
            if (!Schema::hasColumn('services', 'base_price')) {
                $table->decimal('base_price', 10, 2)->after('special_requirements');
            }
            if (!Schema::hasColumn('services', 'duration')) {
                $table->integer('duration')->after('base_price');
            }
            if (!Schema::hasColumn('services', 'variable_pricing')) {
                $table->json('variable_pricing')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('services', 'add_ons')) {
                $table->json('add_ons')->nullable()->after('variable_pricing');
            }
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            // Remove new columns if they exist
            $columns = [
                'category',
                'pet_types',
                'size_ranges',
                'breed_specific',
                'special_requirements',
                'base_price',
                'duration',
                'variable_pricing',
                'add_ons'
            ];

            $existingColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $existingColumns[] = $column;
                }
            }

            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }

            // Add back old price column if it doesn't exist
            if (!Schema::hasColumn('services', 'price')) {
                $table->decimal('price', 10, 2)->after('description');
            }
        });
    }
}; 