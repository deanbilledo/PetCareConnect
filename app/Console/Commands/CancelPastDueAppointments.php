<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelPastDueAppointments extends Command
{
    protected $signature = 'appointments:cancel-past-due';
    protected $description = 'Cancel all pending appointments that are past their scheduled date';

    public function handle()
    {
        try {
            $this->info('Starting to check for past due appointments...');
            Log::info('Starting automatic cancellation of past due appointments');

            $cutoffDate = Carbon::now()->subDay();
            
            $pastDueAppointments = Appointment::where('status', 'pending')
                ->where('appointment_date', '<', $cutoffDate)
                ->get();

            $this->info("Found {$pastDueAppointments->count()} past due appointments");
            Log::info("Found {$pastDueAppointments->count()} past due appointments", [
                'cutoff_date' => $cutoffDate->toDateTimeString()
            ]);

            $count = 0;
            foreach ($pastDueAppointments as $appointment) {
                try {
                    $appointment->update([
                        'status' => 'cancelled',
                        'cancellation_reason' => 'Automatically cancelled due to being past due',
                        'cancelled_at' => now()
                    ]);

                    Log::info("Cancelled past due appointment", [
                        'appointment_id' => $appointment->id,
                        'shop_id' => $appointment->shop_id,
                        'user_id' => $appointment->user_id,
                        'scheduled_date' => $appointment->appointment_date
                    ]);

                    $count++;
                } catch (\Exception $e) {
                    Log::error("Failed to cancel appointment {$appointment->id}", [
                        'error' => $e->getMessage(),
                        'appointment_id' => $appointment->id
                    ]);
                    $this->error("Failed to cancel appointment {$appointment->id}: {$e->getMessage()}");
                }
            }

            $this->info("Successfully cancelled {$count} past due appointments.");
            Log::info("Completed automatic cancellation process", [
                'total_found' => $pastDueAppointments->count(),
                'total_cancelled' => $count
            ]);

        } catch (\Exception $e) {
            Log::error("Error in automatic cancellation process", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error("Error in cancellation process: {$e->getMessage()}");
            throw $e;
        }
    }
} 