<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\NotificationEmailService;
use Illuminate\Console\Command;

class SendPendingEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-pending-emails {--limit=100 : Maximum number of notifications to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications for unread app notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info("Looking for unread notifications with no email sent yet...");
        
        // Get unread notifications that were created more than 1 minute ago
        // (to avoid race conditions with notifications that are just being created)
        $notifications = Notification::where('status', 'unread')
            ->where('created_at', '<', now()->subMinute())
            ->limit($limit)
            ->get();
            
        $count = $notifications->count();
        $this->info("Found {$count} notifications to process");
        
        if ($count === 0) {
            return 0;
        }
        
        $successCount = NotificationEmailService::sendBatchEmailNotifications($notifications);
        
        $this->info("Successfully sent {$successCount} of {$count} email notifications");
        
        return 0;
    }
} 