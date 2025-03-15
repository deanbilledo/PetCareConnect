<?php

namespace App\Console\Commands;

use App\Mail\NotificationEmail;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class TestNotificationEmailCommand extends Command
{
    protected $signature = 'notification:test-email {email? : The email to send the test to} {--debug : Show detailed debug information}';
    protected $description = 'Tests the notification email system by creating and sending a test notification';

    public function handle()
    {
        $email = $this->argument('email') ?? null;
        $debug = $this->option('debug');
        
        // If no email provided, use the first admin's email or the mail.from.address
        if (!$email) {
            $user = User::where('role', 'admin')->first() ?? User::first();
            $email = $user ? $user->email : config('mail.from.address');
        }
        
        $this->info("Starting notification email test to: {$email}");
        
        if ($debug) {
            // Show mail configuration
            $this->info("Mail Configuration:");
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Driver', config('mail.default')],
                    ['SMTP Host', config('mail.mailers.smtp.host')],
                    ['SMTP Port', config('mail.mailers.smtp.port')],
                    ['Encryption', config('mail.mailers.smtp.encryption')],
                    ['Username', config('mail.mailers.smtp.username')],
                    ['Password Set', config('mail.mailers.smtp.password') ? 'Yes' : 'No'],
                    ['From Address', config('mail.from.address')],
                    ['From Name', config('mail.from.name')],
                ]
            );
        }
        
        try {
            // Try different methods in sequence
            $methods = ['service', 'direct', 'raw'];
            $success = false;
            
            foreach ($methods as $method) {
                if ($success) break;
                
                $this->info("Trying method: {$method}...");
                
                switch ($method) {
                    case 'service':
                        // Find or create a user with this email
                        $user = User::where('email', $email)->first();
                        
                        if (!$user) {
                            $this->warn("No user found with email {$email}. Creating a temporary user...");
                            $user = User::create([
                                'name' => 'Test User',
                                'email' => $email,
                                'password' => bcrypt('password'),
                                'role' => 'admin'
                            ]);
                        }
                        
                        $this->info("Creating notification for user...");
                        
                        // Create a notification for the user
                        $notification = $user->notifications()->create([
                            'type' => 'system',
                            'title' => 'Test Notification',
                            'message' => 'This is a test notification to verify that email notifications are working.',
                            'action_url' => url('/'),
                            'action_text' => 'Visit Site',
                            'status' => 'unread',
                            'icon' => 'system'
                        ]);
                        
                        $this->info("Sending notification email via service...");
                        $result = NotificationEmailService::sendEmailNotification($notification);
                        
                        if ($result) {
                            $this->info("Notification email sent successfully via service!");
                            $success = true;
                        } else {
                            $this->warn("Failed to send notification email through service. Trying another method...");
                        }
                        break;
                        
                    case 'direct':
                        // Create a temporary notification
                        $this->info("Creating a temporary notification...");
                        $notification = new Notification([
                            'type' => 'system',
                            'title' => 'Direct Test Notification',
                            'message' => 'This is a direct test email to verify email functionality.',
                            'action_url' => url('/'),
                            'action_text' => 'Visit Site',
                            'status' => 'unread',
                            'icon' => 'system'
                        ]);
                        
                        $this->info("Sending via direct Mail facade...");
                        try {
                            Mail::to($email)->send(new NotificationEmail($notification));
                            $this->info("Direct email sent successfully!");
                            $success = true;
                        } catch (\Exception $e) {
                            $this->warn("Direct method failed: " . $e->getMessage());
                        }
                        break;
                        
                    case 'raw':
                        // Try simple raw email as a last resort
                        $this->info("Sending a simple raw text email...");
                        try {
                            config(['mail.mailers.smtp.timeout' => 30]);
                            Mail::raw("This is a simple text email test from PetCareConnect sent at " . now(), function($message) use ($email) {
                                $message->to($email)
                                        ->subject('PetCareConnect Simple Test - ' . now()->format('H:i:s'));
                            });
                            $this->info("Raw email sent successfully!");
                            $success = true;
                        } catch (\Exception $e) {
                            $this->error("Raw method failed: " . $e->getMessage());
                            if ($debug) {
                                $this->line("Error details: " . $e->getTraceAsString());
                            }
                        }
                        break;
                }
            }
            
            if ($success) {
                $this->info("==============================================");
                $this->info("✅ Email test completed successfully! Please check {$email} inbox (including spam folder).");
                $this->info("==============================================");
                return 0;
            } else {
                $this->error("==============================================");
                $this->error("❌ All email methods failed. Please check your mail configuration.");
                $this->error("==============================================");
                
                // Suggest using Mailtrap instead
                $this->line("");
                $this->info("💡 Suggestion: Consider using Mailtrap for testing instead of Gmail.");
                $this->line("   1. Create a free account at https://mailtrap.io");
                $this->line("   2. Get your SMTP credentials from the inbox settings");
                $this->line("   3. Update your .env file with the Mailtrap configuration");
                $this->line("");
                
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error during test: " . $e->getMessage());
            if ($debug) {
                $this->line("Stack trace: " . $e->getTraceAsString());
            }
            
            Log::error('Notification email test failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 