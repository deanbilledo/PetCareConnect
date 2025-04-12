<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ is_string($notification->title) ? $notification->title : 'Notification' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f7f7f7; color: #333333;">
    <!-- Gmail-optimized table layout -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f7f7f7;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- Email container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e5e5e5;">
                    <!-- Header with Logo -->
                    <tr>
                        <td align="center" style="padding: 30px 40px; background-color: #3498db; border-radius: 8px 8px 0 0;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">{{ config('app.name') }}</h1>
                            <p style="color: #ffffff; margin: 10px 0 0; font-size: 16px; opacity: 0.9;">Your trusted pet care service partner</p>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px;">
                            <!-- Notification Title -->
                            <h2 style="margin: 0 0 20px; font-size: 22px; color: #222222; border-bottom: 1px solid #eeeeee; padding-bottom: 10px;">
                                {{ is_string($notification->title) ? $notification->title : 'Notification' }}
                            </h2>
                            
                            <!-- Notification Message -->
                            <p style="margin: 0 0 25px; font-size: 16px; line-height: 1.6; color: #444444;">
                                {{ is_string($message) ? $message : 'You have received a new notification.' }}
                            </p>
                            
                            <!-- Notification Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px; background-color: #f5f8fa; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #666666;">
                                                    <strong>Date:</strong> {{ date('F j, Y, g:i a') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #666666;">
                                                    <strong>Type:</strong> {{ is_string($notification->type) ? ucfirst(str_replace('_', ' ', $notification->type)) : 'System Notification' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #666666;">
                                                    <strong>Status:</strong> {{ is_string($notification->status) ? ucfirst($notification->status) : 'New' }}
                                                </td>
                                            </tr>
                                            @if(isset($notification->appointment_date) && !empty($notification->appointment_date))
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #666666;">
                                                    <strong>Appointment Date:</strong> {{ $notification->appointment_date->format('F j, Y, g:i a') }}
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Action Button -->
                            @if(isset($actionUrl) && is_string($actionUrl) && !empty($actionUrl) && isset($actionText) && is_string($actionText) && !empty($actionText))
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-radius: 4px; background-color: #3498db;" align="center">
                                                    <a href="{{ $actionUrl }}" target="_blank" style="display: inline-block; padding: 12px 24px; font-size: 16px; color: #ffffff; text-decoration: none; font-weight: bold;">
                                                        {{ $actionText }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif
                            
                            <!-- Help Text -->
                            <p style="margin: 20px 0 0; font-size: 14px; color: #777777;">
                                If you need any assistance, please don't hesitate to contact our support team or reply to this email.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Pet Graphic - Optional -->
                    <tr>
                        <td align="center" style="padding: 10px 40px 30px;">
                            <img src="{{ url('/images/pet-icon. png') }}" alt="Pet Icon" style="max-width: 100px; height: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: #f5f5f5; border-top: 1px solid #e1e1e1; border-radius: 0 0 8px 8px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding: 0 0 10px; font-size: 14px; color: #888888;">
                                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 0 0 10px; font-size: 14px; color: #888888;">
                                        This email was sent to you because you have an account with {{ config('app.name') }}.
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 10px 0; font-size: 14px; color: #888888;">
                                        <a href="{{ url('/contact') }}" style="color: #3498db; text-decoration: underline;">Contact Support</a> • 
                                        <a href="{{ url('/settings') }}" style="color: #3498db; text-decoration: underline;">Email Preferences</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 10px 0 0; font-size: 14px; color: #888888;">
                                        <a href="https://www.facebook.com/profile.php?id=61568046598637" style="color: #3498db; text-decoration: none; margin: 0 10px; font-weight: bold;">Facebook</a> • 
                                        <a href="#" style="color: #3498db; text-decoration: none; margin: 0 10px; font-weight: bold;">Instagram</a> • 
                                        <a href="#" style="color: #3498db; text-decoration: none; margin: 0 10px; font-weight: bold;">Twitter</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- End Email container -->
            </td>
        </tr>
    </table>
</body>
</html> 