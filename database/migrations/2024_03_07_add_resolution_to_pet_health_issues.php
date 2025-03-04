<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pet_health_issues', function (Blueprint $table) {
            $table->boolean('is_resolved')->default(false)->after('vet_notes');
            $table->timestamp('resolved_date')->nullable()->after('is_resolved');
        });
    }

    public function down(): void
    {
        Schema::table('pet_health_issues', function (Blueprint $table) {
            $table->dropColumn(['is_resolved', 'resolved_date']);
        });
    }
}; 