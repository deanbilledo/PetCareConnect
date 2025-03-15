<?php

namespace App\Services;

use App\Mail\NotificationEmail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class NotificationEmailService
{
    /**
     * Send an email notification to the user
     *
     * @param Notification $notification
     * @return bool
     */
    public static function sendEmailNotification(Notification $notification): bool
    {
        // Get the notifiable user
        $notifiable = $notification->notifiable;
        
        // Only send if the notifiable is a User and has an email
        if (!$notifiable instanceof User || !$notifiable->email) {
            Log::warning('Cannot send notification email: notifiable is not a user or has no email', [
                'notification_id' => $notification->id,
                'notifiable_type' => $notification->notifiable_type,
                'notifiable_id' => $notification->notifiable_id,
            ]);
            return false;
        }
        
        try {
            // Add debugging information
            Log::info('Attempting to send email notification', [
                'notification_id' => $notification->id,
                'recipient' => $notifiable->email,
                'type' => $notification->type,
                'title' => $notification->title,
                'smtp_host' => config('mail.mailers.smtp.host'),
                'mail_from' => config('mail.from.address'),
            ]);
            
            // Create NotificationEmail instance first to handle any potential errors
            $email = new NotificationEmail($notification);
            
            // Send the email immediately with explicit timeout setting
            Mail::mailer('smtp')
                ->to($notifiable->email)
                ->send($email);
                
            Log::info('Email notification sent successfully', [
                'notification_id' => $notification->id,
                'recipient' => $notifiable->email,
                'type' => $notification->type,
                'title' => $notification->title
            ]);
                
            return true;
        } catch (\Swift_TransportException $e) {
            // Handle SMTP connection issues specifically
            Log::error('SMTP connection error while sending email notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id,
                'recipient' => $notifiable->email,
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port')
            ]);
            report($e);
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id,
                'recipient' => $notifiable->email,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            report($e);
            return false;
        }
    }
    
    /**
     * Send email notifications for a batch of notifications
     *
     * @param \Illuminate\Database\Eloquent\Collection $notifications
     * @return int Number of successfully sent emails
     */
    public static function sendBatchEmailNotifications($notifications): int
    {
        $successCount = 0;
        
        foreach ($notifications as $notification) {
            if (self::sendEmailNotification($notification)) {
                $successCount++;
            }
        }
        
        return $successCount;
    }
    
    /**
     * Debug email configuration and try to send a test email
     *
     * @param string $toEmail
     * @return array
     */
    public static function debugEmailConfiguration(string $toEmail): array
    {
        $result = [
            'status' => 'unknown',
            'message' => '',
            'config' => [],
            'error' => null
        ];
        
        // Get mail configuration
        $result['config'] = [
            'driver' => Config::get('mail.default'),
            'host' => Config::get('mail.mailers.smtp.host'),
            'port' => Config::get('mail.mailers.smtp.port'),
            'encryption' => Config::get('mail.mailers.smtp.encryption'),
            'username' => Config::get('mail.mailers.smtp.username'),
            'password' => Config::get('mail.mailers.smtp.password') ? '[SET]' : '[NOT SET]',
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
        ];
        
        try {
            // Try to send a simple text email first for better debugging
            Log::info('Attempting to send test email', ['to' => $toEmail]);
            
            // Set a longer timeout for Gmail connections
            config(['mail.mailers.smtp.timeout' => 30]);
            
            Mail::raw('This is a test email to verify email integration with PetCareConnect. Sent at: ' . now(), function($message) use ($toEmail) {
                $message->to($toEmail)
                        ->subject('PetCareConnect Email Test - ' . now()->format('Y-m-d H:i:s'));
            });
            
            $result['status'] = 'success';
            $result['message'] = "Test email sent to {$toEmail}. Please check your inbox and spam folder.";
            
            Log::info('Email test successful', [
                'to' => $toEmail,
                'config' => $result['config']
            ]);
        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Failed to send test email: ' . $e->getMessage();
            $result['error'] = $e->getMessage();
            
            Log::error('Email test failed', [
                'to' => $toEmail,
                'config' => $result['config'],
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return $result;
    }
}