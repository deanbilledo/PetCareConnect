Schema::create('users', function (Blueprint $table) {
    // ... other columns ...
    $table->string('profile_photo_path')->nullable();
});
