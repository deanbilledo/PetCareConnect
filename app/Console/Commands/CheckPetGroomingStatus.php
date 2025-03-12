<?php

namespace App\Console\Commands;

use App\Models\Pet;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckPetGroomingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pets:check-grooming {--days=30 : Days since last grooming to trigger notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pets that have not been groomed recently and send notifications to owners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysThreshold = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($daysThreshold);
        
        $this->info("Checking for pets not groomed since {$cutoffDate->toDateString()}...");
        
        // Get all pets with active users
        $pets = Pet::whereNull('death_date')
            ->whereHas('user', function($query) {
                $query->where('status', 'active');
            })
            ->get();
        
        $this->info("Found {$pets->count()} active pets to check.");
        
        $notificationCount = 0;
        
        foreach ($pets as $pet) {
            try {
                // Get the most recent grooming appointment for this pet
                $lastGrooming = Appointment::where('status', 'completed')
                    ->whereHas('services', function($query) {
                        $query->where('category', 'grooming');
                    })
                    ->whereHas('pets', function($query) use ($pet) {
                        $query->where('pets.id', $pet->id);
                    })
                    ->latest('appointment_date')
                    ->first();
                
                // If pet has no grooming appointment or last appointment is older than threshold
                if (!$lastGrooming || $lastGrooming->appointment_date->lt($cutoffDate)) {
                    // Determine timeframe message based on last grooming date
                    if ($lastGrooming) {
                        $daysSince = $lastGrooming->appointment_date->diffInDays(Carbon::now());
                        $timeframe = $this->formatTimeframe($daysSince);
                        $lastGroomingDate = $lastGrooming->appointment_date->format('M d, Y');
                        $message = "{$pet->name} hasn't been groomed in {$timeframe}. Last grooming was on {$lastGroomingDate}.";
                    } else {
                        $message = "{$pet->name} has no record of grooming appointments. Regular grooming is important for your pet's health.";
                    }
                    
                    // Create notification for pet owner
                    $pet->user->notifications()->create([
                        'type' => 'pet_care',
                        'title' => 'Grooming Reminder',
                        'message' => $message,
                        'action_url' => route('profile.pets.show', $pet),
                        'action_text' => 'View Pet Details',
                        'status' => 'unread'
                    ]);
                    
                    $notificationCount++;
                    $this->info("Created grooming reminder notification for {$pet->name} (Owner: {$pet->user->name})");
                }
            } catch (\Exception $e) {
                Log::error("Error processing grooming check for pet #{$pet->id}: " . $e->getMessage());
                $this->error("Error checking pet #{$pet->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Completed! Created {$notificationCount} grooming reminder notifications.");
        
        return 0;
    }
    
    /**
     * Format the timeframe in a human-readable format
     */
    private function formatTimeframe($days)
    {
        if ($days < 7) {
            return "{$days} days";
        } elseif ($days < 30) {
            $weeks = floor($days / 7);
            return $weeks == 1 ? "1 week" : "{$weeks} weeks";
        } elseif ($days < 365) {
            $months = floor($days / 30);
            return $months == 1 ? "1 month" : "{$months} months";
        } else {
            $years = floor($days / 365);
            $extraMonths = floor(($days % 365) / 30);
            
            if ($extraMonths == 0) {
                return $years == 1 ? "1 year" : "{$years} years";
            } else {
                $yearText = $years == 1 ? "1 year" : "{$years} years";
                $monthText = $extraMonths == 1 ? "1 month" : "{$extraMonths} months";
                return "{$yearText} and {$monthText}";
            }
        }
    }
} 