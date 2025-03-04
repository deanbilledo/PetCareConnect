<?php

namespace App\Console\Commands;

use App\Models\Pet;
use Illuminate\Console\Command;

class CheckPetHealthDueDates extends Command
{
    protected $signature = 'pets:check-health-due-dates';
    protected $description = 'Check for upcoming and overdue vaccinations and parasite treatments';

    public function handle()
    {
        $this->info('Checking pet health due dates...');

        $now = now();
        $sevenDaysFromNow = $now->copy()->addDays(7);

        // Get all active pets (not deceased)
        Pet::whereNull('death_date')->chunk(100, function ($pets) use ($now, $sevenDaysFromNow) {
            foreach ($pets as $pet) {
                // Check vaccinations
                foreach ($pet->vaccinations as $vaccination) {
                    if ($vaccination->next_due_date->between($now, $sevenDaysFromNow)) {
                        $pet->user->notifications()->create([
                            'type' => 'appointment',
                            'title' => 'Upcoming Vaccination Due',
                            'message' => "Vaccination '{$vaccination->vaccine_name}' for {$pet->name} is due on {$vaccination->next_due_date->format('M d, Y')}",
                            'action_url' => route('profile.pets.health-record', $pet),
                            'action_text' => 'View Health Record'
                        ]);
                    } elseif ($vaccination->next_due_date->isPast()) {
                        $pet->user->notifications()->create([
                            'type' => 'appointment',
                            'title' => 'Overdue Vaccination',
                            'message' => "Vaccination '{$vaccination->vaccine_name}' for {$pet->name} was due on {$vaccination->next_due_date->format('M d, Y')}",
                            'action_url' => route('profile.pets.health-record', $pet),
                            'action_text' => 'View Health Record'
                        ]);
                    }
                }

                // Check parasite controls
                foreach ($pet->parasiteControls as $control) {
                    if ($control->next_treatment_date->between($now, $sevenDaysFromNow)) {
                        $pet->user->notifications()->create([
                            'type' => 'appointment',
                            'title' => 'Upcoming Parasite Treatment Due',
                            'message' => "Parasite treatment '{$control->treatment_name}' for {$pet->name} is due on {$control->next_treatment_date->format('M d, Y')}",
                            'action_url' => route('profile.pets.health-record', $pet),
                            'action_text' => 'View Health Record'
                        ]);
                    } elseif ($control->next_treatment_date->isPast()) {
                        $pet->user->notifications()->create([
                            'type' => 'appointment',
                            'title' => 'Overdue Parasite Treatment',
                            'message' => "Parasite treatment '{$control->treatment_name}' for {$pet->name} was due on {$control->next_treatment_date->format('M d, Y')}",
                            'action_url' => route('profile.pets.health-record', $pet),
                            'action_text' => 'View Health Record'
                        ]);
                    }
                }
            }
        });

        $this->info('Pet health due dates check completed!');
    }
} 