<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Ensure InnoDB engine for foreign key support
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('veterinarian_id');
            $table->unsignedBigInteger('shop_id')->nullable(); // Nullable foreign key for shops
            $table->unsignedTinyInteger('rating'); // Rating value between 1 and 5
            $table->timestamps(); // Created and updated timestamps

            // Foreign key constraints
            $table->foreign('veterinarian_id')->references('id')->on('veterinarians')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade'); // Optional foreign key for shop
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
