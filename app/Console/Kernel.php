<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These cron jobs are run in the background by a cron job on the server.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('appointments:cancel-past-due')
                ->daily()
                ->at('00:01')
                ->appendOutputTo(storage_path('logs/scheduler.log'))
                ->onSuccess(function () {
                    Log::info('Automatic appointment cancellation completed successfully');
                })
                ->onFailure(function () {
                    Log::error('Automatic appointment cancellation failed');
                })
                ->emailOutputOnFailure(env('ADMIN_EMAIL'))
                ->runInBackground();

        // Check for upcoming appointments every 5 minutes
        $schedule->call(function () {
            app(\App\Http\Controllers\AppointmentController::class)->checkUpcomingAppointments();
        })->everyFiveMinutes();

        // Check pet health due dates daily at 8 AM
        $schedule->command('pets:check-health-due-dates')
                ->dailyAt('08:00')
                ->withoutOverlapping();

        // Check for pets that need grooming - run daily at 8:00 AM
        $schedule->command('pets:check-grooming')->dailyAt('08:00');
        
        // Send pending email notifications every 2 minutes
        $schedule->command('notifications:send-pending-emails')
                ->everyTwoMinutes()
                ->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/email-notifications.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 