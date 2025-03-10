<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscription;
use App\Models\Shop;

class ShopSubscriptionCancelled extends Notification
{
    use Queueable;

    protected $subscription;
    protected $shop;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription, Shop $shop)
    {
        $this->subscription = $subscription;
        $this->shop = $shop;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Shop Subscription Cancelled')
                    ->line('A shop has cancelled their subscription.')
                    ->line('Shop: ' . $this->shop->name)
                    ->line('Subscription ID: ' . $this->subscription->id)
                    ->action('View Payments', url('/admin/payments'))
                    ->line('This is an automated notification.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'shop_id' => $this->shop->id,
            'shop_name' => $this->shop->name,
            'message' => $this->shop->name . ' has cancelled their subscription.',
            'type' => 'shop_subscription_cancelled'
        ];
    }
} 