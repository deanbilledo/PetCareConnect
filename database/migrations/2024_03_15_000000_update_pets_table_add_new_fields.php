<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pets', function (Blueprint $table) {
            // Only drop height column if it exists
            if (Schema::hasColumn('pets', 'height')) {
                $table->dropColumn('height');
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('pets', 'size_category')) {
                $table->string('size_category')->nullable()->after('breed');
            }
            if (!Schema::hasColumn('pets', 'color_markings')) {
                $table->string('color_markings')->nullable()->after('weight');
            }
            if (!Schema::hasColumn('pets', 'coat_type')) {
                $table->string('coat_type')->nullable()->after('color_markings');
            }
            if (!Schema::hasColumn('pets', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('coat_type');
            }
            
            // Modify weight column to be decimal
            if (Schema::hasColumn('pets', 'weight')) {
                $table->decimal('weight', 5, 2)->change();
            }
        });

        // Update existing records with default values if needed
        DB::table('pets')->update([
            'size_category' => 'Medium',
            'color_markings' => 'Not specified',
            'coat_type' => 'Not specified',
            'date_of_birth' => now()->subYears(1) // Set to 1 year ago as default
        ]);

        // Now make the columns required
        Schema::table('pets', function (Blueprint $table) {
            $table->string('size_category')->nullable(false)->change();
            $table->string('color_markings')->nullable(false)->change();
            $table->string('coat_type')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            // Only drop columns if they exist
            $columns = ['size_category', 'color_markings', 'coat_type', 'date_of_birth'];
            $existingColumns = [];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('pets', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }

            // Only add height and modify weight if they don't exist
            if (!Schema::hasColumn('pets', 'height')) {
                $table->string('height')->after('weight');
            }
            if (Schema::hasColumn('pets', 'weight')) {
                $table->string('weight')->change();
            }
        });
    }
}; 