<?php

namespace App\Traits;

use App\Models\Notification;
use App\Services\NotificationEmailService;
use Illuminate\Support\Facades\Log;

trait NotifiesWithEmail
{
    /**
     * Create a notification and send an email notification.
     *
     * @param array $data The notification data
     * @return Notification
     */
    public function notifyWithEmail(array $data): Notification
    {
        // Create the notification
        $notification = $this->notifications()->create($data);
        
        try {
            // Send the email notification
            $emailResult = NotificationEmailService::sendEmailNotification($notification);
            
            if ($emailResult) {
                Log::info('Email notification sent successfully', [
                    'notifiable_type' => get_class($this),
                    'notifiable_id' => $this->id,
                    'notification_id' => $notification->id,
                    'notification_type' => $notification->type
                ]);
            } else {
                Log::warning('Email notification was not sent', [
                    'notifiable_type' => get_class($this),
                    'notifiable_id' => $this->id,
                    'notification_id' => $notification->id,
                    'notification_type' => $notification->type
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the notification creation
            Log::error('Exception while sending email notification', [
                'notifiable_type' => get_class($this),
                'notifiable_id' => $this->id,
                'notification_id' => $notification->id, 
                'notification_type' => $notification->type,
                'error' => $e->getMessage()
            ]);
        }
        
        return $notification;
    }
    
    /**
     * Create multiple notifications and send email notifications for each.
     *
     * @param array $notificationsData Array of notification data arrays
     * @return array Array of created notifications
     */
    public function notifyManyWithEmail(array $notificationsData): array
    {
        $notifications = [];
        
        foreach ($notificationsData as $data) {
            $notifications[] = $this->notifyWithEmail($data);
        }
        
        return $notifications;
    }
} 