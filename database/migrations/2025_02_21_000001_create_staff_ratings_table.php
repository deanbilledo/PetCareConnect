<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('staff_ratings')) {
            Schema::create('staff_ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('appointment_id');
                $table->integer('rating');
                $table->text('review')->nullable();
                $table->timestamps();
                
                // Add foreign keys
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                
                $table->foreign('employee_id')
                    ->references('id')
                    ->on('employees')
                    ->onDelete('cascade');
                
                $table->foreign('appointment_id')
                    ->references('id')
                    ->on('appointments')
                    ->onDelete('cascade');
                
                // Ensure one rating per user per employee per appointment
                $table->unique(['user_id', 'employee_id', 'appointment_id'], 'unique_staff_rating');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('staff_ratings');
    }
}; 