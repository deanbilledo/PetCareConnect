<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The notification instance.
     *
     * @var \App\Models\Notification
     */
    public $notification;
    
    /**
     * The notification message.
     *
     * @var string
     */
    public $messageText;
    
    /**
     * The action text.
     *
     * @var string|null
     */
    public $actionText;
    
    /**
     * The action URL.
     *
     * @var string|null
     */
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        
        // Ensure properties are strings
        $this->messageText = is_string($notification->message) ? $notification->message : '';
        $this->actionText = is_string($notification->action_text) ? $notification->action_text : '';
        $this->actionUrl = is_string($notification->action_url) ? $notification->action_url : '';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notification->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'notification' => $this->notification,
                'message' => $this->messageText,
                'actionText' => $this->actionText,
                'actionUrl' => $this->actionUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 