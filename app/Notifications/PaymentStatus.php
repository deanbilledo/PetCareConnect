<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscription;
use App\Models\Shop;
use App\Services\NotificationEmailService;

class PaymentStatus extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscription;
    protected $shop;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription, Shop $shop, string $status)
    {
        $this->subscription = $subscription;
        $this->shop = $shop;
        $this->status = $status; // verified, rejected, pending
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Only use mail channel, we'll manually create database notification
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Payment ' . ucfirst($this->status) . ' for ' . $this->shop->name)
                    ->view('emails.payment-status', [
                        'user' => $notifiable,
                        'shop' => $this->shop,
                        'subscription' => $this->subscription,
                        'status' => $this->status
                    ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     * Note: Not using this directly since we have a custom notification system.
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messages = [
            'verified' => 'Your payment has been verified. Your subscription is now active.',
            'rejected' => 'Your payment has been rejected. Please submit a new payment.',
            'pending' => 'Your payment is being processed. We will notify you once it has been verified.'
        ];

        $titles = [
            'verified' => 'Payment Verified',
            'rejected' => 'Payment Rejected', 
            'pending' => 'Payment Pending'
        ];

        $actionTexts = [
            'verified' => 'View Subscription',
            'rejected' => 'Resubmit Payment',
            'pending' => 'View Payment Status'
        ];

        $icons = [
            'verified' => 'check-circle',
            'rejected' => 'x-circle',
            'pending' => 'clock'
        ];

        return [
            'type' => 'payment_' . $this->status,
            'title' => $titles[$this->status] ?? 'Payment Status Updated',
            'message' => $messages[$this->status] . ' Amount: ₱' . number_format($this->subscription->amount, 2),
            'action_url' => route('shop.subscriptions.index'),
            'action_text' => $actionTexts[$this->status] ?? 'View Details',
            'icon' => $icons[$this->status] ?? 'bell',
            'status' => 'unread',
            
            // Additional data for reference (not required by database schema)
            'subscription_id' => $this->subscription->id,
            'shop_id' => $this->shop->id,
            'shop_name' => $this->shop->name,
            'amount' => $this->subscription->amount,
            'start_date' => $this->subscription->subscription_starts_at,
            'end_date' => $this->subscription->subscription_ends_at,
            'reference_number' => $this->subscription->reference_number
        ];
    }

    /**
     * Get notification data for the application's custom notification system
     * 
     * @return array
     */
    public function toNotifyWithEmail(): array
    {
        $messages = [
            'verified' => 'Your payment has been verified. Your subscription is now active.',
            'rejected' => 'Your payment has been rejected. Please submit a new payment.',
            'pending' => 'Your payment is being processed. We will notify you once it has been verified.'
        ];

        $titles = [
            'verified' => 'Payment Verified',
            'rejected' => 'Payment Rejected', 
            'pending' => 'Payment Pending'
        ];

        $actionTexts = [
            'verified' => 'View Subscription',
            'rejected' => 'Resubmit Payment',
            'pending' => 'View Payment Status'
        ];

        $icons = [
            'verified' => 'check-circle',
            'rejected' => 'x-circle',
            'pending' => 'clock'
        ];
        
        return [
            'type' => 'payment_' . $this->status,
            'title' => $titles[$this->status] ?? 'Payment Status Updated',
            'message' => $messages[$this->status] . ' Amount: ₱' . number_format($this->subscription->amount, 2),
            'action_url' => route('shop.subscriptions.index'),
            'action_text' => $actionTexts[$this->status] ?? 'View Details',
            'icon' => $icons[$this->status] ?? 'bell',
            'status' => 'unread',
            'read_at' => null
        ];
    }
} 