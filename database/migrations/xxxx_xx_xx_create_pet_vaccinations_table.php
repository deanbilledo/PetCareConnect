<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pet_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->string('vaccine_name');
            $table->string('veterinarian');
            $table->date('date');
            $table->date('next_due_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pet_vaccinations');
    }
}; 