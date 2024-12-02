<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->json('pet_types');
            $table->json('size_ranges');
            $table->boolean('breed_specific')->default(false);
            $table->text('special_requirements')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->integer('duration');
            $table->json('variable_pricing')->nullable();
            $table->json('add_ons')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}; 