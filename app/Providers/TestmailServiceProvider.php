<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Http;

class TestmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for sent mails to capture and forward to testmail.app
        Event::listen(function (MessageSent $event) {
            // Get API credentials
            $apiKey = config('services.testmail.api_key');
            $namespace = config('services.testmail.namespace');
            
            if (empty($apiKey) || empty($namespace)) {
                Log::error('Testmail.app API key or namespace not configured');
                return;
            }
            
            try {
                // Special handling for password reset notifications
                $toEmail = null;
                
                // If this is a password reset notification, grab the email from session
                if (session()->has('password_reset_email')) {
                    $toEmail = session('password_reset_email');
                    Log::info('Found password reset email in session', ['email' => $toEmail]);
                    // Clear the session to avoid using it again
                    session()->forget('password_reset_email');
                }
                
                // Get the message
                $message = $event->message;
                
                // Get recipient details - need to handle Symfony Address objects
                $recipients = $message->getTo();
                
                if (empty($recipients)) {
                    Log::error('No recipients for email');
                    return;
                }
                
                // Log detailed information about recipients for debugging
                Log::info('Recipients details', [
                    'recipients_type' => gettype($recipients),
                    'recipients_is_array' => is_array($recipients),
                    'recipients_count' => is_countable($recipients) ? count($recipients) : 'not countable',
                    'first_recipient_type' => is_array($recipients) && !empty($recipients) ? gettype(reset($recipients)) : 'none'
                ]);
                
                // Try various methods to extract the email address
                // Method 1: Direct access if it's an array of strings
                if (is_array($recipients) && count($recipients) > 0) {
                    foreach ($recipients as $email => $name) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $toEmail = $email;
                            Log::info('Found email using direct array key', ['email' => $toEmail]);
                            break;
                        }
                    }
                }
                
                // Method 2: Handle Symfony Address objects
                if (!$toEmail && is_array($recipients) && count($recipients) > 0) {
                    foreach ($recipients as $recipient) {
                        if (is_object($recipient) && method_exists($recipient, 'getAddress')) {
                            $toEmail = $recipient->getAddress();
                            Log::info('Found email using getAddress method', ['email' => $toEmail]);
                            break;
                        }
                    }
                }
                
                // Method 3: Use the first index if recipients array is non-associative and contains Address objects
                if (!$toEmail && is_array($recipients) && !empty($recipients) && isset($recipients[0])) {
                    $firstRecipient = $recipients[0];
                    if (is_object($firstRecipient)) {
                        Log::info('Examining first recipient object', [
                            'class' => get_class($firstRecipient),
                            'methods' => get_class_methods($firstRecipient)
                        ]);
                        
                        if (method_exists($firstRecipient, 'getAddress')) {
                            $toEmail = $firstRecipient->getAddress();
                            Log::info('Found email from first recipient object', ['email' => $toEmail]);
                        }
                    }
                }
                
                // Method 4: Just use the email from the password reset if this is a password reset
                if (!$toEmail && $event->data && isset($event->data['notifiable']) && method_exists($event->data['notifiable'], 'getEmailForPasswordReset')) {
                    $toEmail = $event->data['notifiable']->getEmailForPasswordReset();
                    Log::info('Found email using getEmailForPasswordReset method', ['email' => $toEmail]);
                }
                
                // Method 5: Last resort - see if the event has a "to" property
                if (!$toEmail && isset($event->data['message']) && method_exists($event->data['message'], 'getTo')) {
                    $to = $event->data['message']->getTo();
                    if (is_array($to) && count($to) > 0) {
                        $firstKey = array_key_first($to);
                        if (filter_var($firstKey, FILTER_VALIDATE_EMAIL)) {
                            $toEmail = $firstKey;
                        }
                    }
                }
                
                // Log detailed information to help debug
                Log::info('Message details for debugging', [
                    'recipients_type' => gettype($recipients),
                    'recipients_class' => is_object($recipients) ? get_class($recipients) : 'not an object',
                    'recipients_count' => is_countable($recipients) ? count($recipients) : 'not countable',
                    'recipients_dump' => is_array($recipients) ? json_encode($recipients) : 'not an array',
                    'has_session_email' => session()->has('password_reset_email'),
                    'session_email' => session('password_reset_email'),
                    'extracted_email' => $toEmail
                ]);
                
                if (empty($toEmail)) {
                    Log::error('Could not extract email from recipient', ['recipients' => $recipients]);
                    return;
                }
                
                // Extract email content
                $subject = $message->getSubject();
                
                // Handle the body properly - it can be a complex object
                $htmlContent = '';
                $textContent = '';
                
                // Check the type of body
                $body = $message->getBody();
                $bodyType = is_object($body) ? get_class($body) : gettype($body);
                
                Log::info('Message body type', [
                    'body_type' => $bodyType,
                    'is_object' => is_object($body),
                    'is_string' => is_string($body)
                ]);
                
                // Extract HTML and text content based on the type
                if (is_string($body)) {
                    // If it's already a string, use it directly
                    $htmlContent = $body;
                    $textContent = strip_tags($body);
                } elseif (is_object($body)) {
                    // For Symfony objects, try to get the content from them
                    
                    // Special case for AlternativePart which was causing errors
                    if ($body instanceof \Symfony\Component\Mime\Part\Multipart\AlternativePart) {
                        try {
                            $parts = $body->getParts();
                            foreach ($parts as $part) {
                                if ($part->getMediaType() == 'text') {
                                    if ($part->getMediaSubtype() == 'html') {
                                        $htmlContent = $part->getBody();
                                    } else if ($part->getMediaSubtype() == 'plain') {
                                        $textContent = $part->getBody();
                                    }
                                }
                            }
                            
                            // Log what we found
                            Log::info('Extracted content from AlternativePart', [
                                'html_length' => strlen($htmlContent ?? ''),
                                'text_length' => strlen($textContent ?? '')
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error processing AlternativePart: ' . $e->getMessage());
                        }
                    }
                    // Generic handlers for other object types
                    else if (method_exists($body, 'getHtmlBody')) {
                        $htmlContent = $body->getHtmlBody() ?: '';
                    } else if (method_exists($body, 'getBody')) {
                        $htmlContent = $body->getBody() ?: '';
                    } else if (method_exists($body, 'toString')) {
                        $htmlContent = $body->toString() ?: '';
                    } else if (method_exists($body, '__toString')) {
                        $htmlContent = (string)$body;
                    }
                    
                    // Get plain text version for other object types
                    if (empty($textContent)) {
                        if (method_exists($body, 'getTextBody')) {
                            $textContent = $body->getTextBody() ?: '';
                        } else {
                            // Only strip tags if we have HTML content as a string
                            $textContent = is_string($htmlContent) ? strip_tags($htmlContent) : '';
                        }
                    }
                }
                
                // Ensure we have content
                if (empty($htmlContent) && empty($textContent)) {
                    Log::error('Could not extract email content', [
                        'body_type' => $bodyType,
                        'body_methods' => is_object($body) ? get_class_methods($body) : 'not an object'
                    ]);
                    return;
                }
                
                // Since we're using the log mailer, the email content will be written to Laravel's logs
                // TestMail.app is used for viewing emails that would be sent in production, not for sending emails
                Log::info('Email is being logged (using log driver)', [
                    'to' => $toEmail,
                    'from' => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
                    'subject' => $subject,
                    'html_length' => strlen($htmlContent),
                    'text_length' => strlen($textContent)
                ]);
                
                // After sending, query TestMail.app API to check for emails
                $apiEndpoint = "https://api.testmail.app/api/json";
                
                try {
                    // Query the API to check if our emails are being received
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey
                    ])->get($apiEndpoint, [
                        'apikey' => $apiKey,
                        'namespace' => $namespace,
                        'pretty' => 'true',
                        'limit' => 5, // Get the 5 most recent emails
                        'tag' => 'password-reset'
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        $emailCount = $data['count'] ?? 0;
                        
                        Log::info('TestMail.app inbox status', [
                            'email_count' => $emailCount,
                            'check_url' => "https://testmail.app/console/{$namespace}/emails"
                        ]);
                        
                        if ($emailCount > 0) {
                            Log::info('Found emails in TestMail.app inbox', [
                                'count' => $emailCount,
                                'first_subject' => $data['emails'][0]['subject'] ?? 'Unknown'
                            ]);
                        } else {
                            Log::info('No emails found in TestMail.app inbox yet - they may take a moment to appear');
                        }
                    } else {
                        Log::error('Failed to query TestMail.app API', [
                            'status' => $response->status(),
                            'message' => $response->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking TestMail.app inbox: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                Log::error('Error forwarding email to testmail.app: ' . $e->getMessage(), [
                    'exception' => $e->getTraceAsString()
                ]);
            }
        });
        
        // Override password reset notifications with custom text
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $email = $notifiable->email ?? $notifiable->getEmailForPasswordReset();
            
            Log::info('Password reset requested', [
                'email' => $email,
                'token' => $token
            ]);
            
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            
            // Store the current email in a session to help our event listener
            session(['password_reset_email' => $email]);
            
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Reset Your ' . config('app.name') . ' Password')
                ->greeting('Hello Pet Lover!')
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->action('Reset Password', $resetUrl)
                ->line('This password reset link will expire in 60 minutes.')
                ->line('If you did not request a password reset, no further action is required.')
                ->salutation('Wags and purrs,');
        });
    }
} 