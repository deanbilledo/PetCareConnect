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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->string('type'); // Type of notification (e.g., 'appointment', 'review', 'system')
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable(); // URL to redirect when clicking the notification
            $table->string('action_text')->nullable(); // Text for the action button
            $table->string('icon')->nullable(); // Icon class or path
            $table->string('status')->default('unread'); // unread, read
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index('created_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
