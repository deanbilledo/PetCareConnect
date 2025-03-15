<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email?}';
    protected $description = 'Test email functionality';

    public function handle()
    {
        $recipientEmail = $this->argument('email') ?? env('MAIL_FROM_ADDRESS');
        
        $this->info("Testing email sending to: {$recipientEmail}");
        $this->info("Using mailer: " . config('mail.default'));
        $this->info("SMTP Host: " . config('mail.mailers.smtp.host'));
        
        try {
            Mail::raw('This is a test email from PetCareConnect to verify email functionality.', function($message) use ($recipientEmail) {
                $message->to($recipientEmail)
                        ->subject('Email Test from PetCareConnect');
            });
            
            $this->info('Email sent successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('Email test failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
} 