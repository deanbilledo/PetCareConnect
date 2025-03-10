<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('review')->nullable();
            $table->timestamps();

            // Prevent duplicate ratings from the same user for the same appointment
            $table->unique(['user_id', 'shop_id', 'appointment_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}; 