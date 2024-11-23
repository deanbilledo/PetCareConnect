<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create symbolic link if it doesn't exist
        if(!file_exists(public_path('storage'))) {
            try {
                app('files')->link(
                    storage_path('app/public'), 
                    public_path('storage')
                );
            } catch (\Exception $e) {
                \Log::error('Failed to create storage symlink: ' . $e->getMessage());
            }
        }
    }
}
