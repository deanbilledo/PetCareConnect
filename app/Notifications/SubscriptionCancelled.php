<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Subscription;
use App\Models\Shop;

class SubscriptionCancelled extends Notification
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
                    ->subject('Subscription Cancelled')
                    ->line('Your subscription has been cancelled.')
                    ->line('Premium features have been revoked. You can resubscribe at any time.')
                    ->action('Resubscribe', url('/shop/subscriptions'))
                    ->line('Thank you for using our application!');
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
            'message' => 'You have successfully cancelled your subscription. Premium features have been revoked.',
            'type' => 'subscription_cancelled'
        ];
    }
} 