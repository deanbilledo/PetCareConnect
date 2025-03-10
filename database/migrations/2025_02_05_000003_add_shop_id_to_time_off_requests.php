<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: shop_id is now added in the create_time_off_requests_table migration
    }

    public function down(): void
    {
        // No-op: shop_id is now added in the create_time_off_requests_table migration
    }
}; 