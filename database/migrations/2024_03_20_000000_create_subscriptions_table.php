<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('status')->default('trial'); // trial, active, expired, cancelled
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->decimal('amount', 10, 2)->default(299.00);
            $table->string('reference_number')->nullable();
            $table->string('payment_screenshot')->nullable();
            $table->string('payment_status')->default('pending'); // pending, verified, rejected
            $table->timestamps();

            // Add index and foreign key in the same statement
            $table->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}; 