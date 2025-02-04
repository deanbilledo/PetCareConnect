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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->json('pet_types');
            $table->json('size_ranges');
            $table->boolean('exotic_pet_service')->default(false);
            $table->json('exotic_pet_species')->nullable();
            $table->text('special_requirements')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->integer('duration');
            $table->json('variable_pricing')->nullable();
            $table->json('add_ons')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
}; 