<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Cancelled</title>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            color: #4a5568;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #4F46E5;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header img {
            max-width: 200px;
            height: auto;
        }
        .email-body {
            padding: 30px 20px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        h1 {
            color: #1a202c;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
        }
        p {
            margin-bottom: 16px;
        }
        .button {
            display: inline-block;
            background-color: #4F46E5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #4338ca;
        }
        .subscription-details {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .subscription-details h2 {
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 15px;
            color: #4a5568;
        }
        .pet-images {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pet-images img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 10px;
            object-fit: cover;
            border: 3px solid #e2e8f0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
        }
        .detail-value {
            color: #1a202c;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
        }
        .social-links img {
            width: 24px;
            height: 24px;
        }
        .text-muted {
            color: #718096;
        }
        .status-cancelled {
            display: inline-block;
            background-color: #FEE2E2;
            color: #B91C1C;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <img src="{{ url('images/logo.png') }}" alt="PetCareConnect" />
        </div>
        
        <div class="email-body">
            <h1>Subscription Cancelled</h1>
            
            <p>Hello {{ $user->name }},</p>
            
            <p>Your subscription for <strong>{{ $shop->name }}</strong> has been cancelled successfully.</p>
            
            <div class="pet-images">
                <img src="{{ url('images/dog1.png') }}" alt="Pet" />
                <img src="{{ url('images/dog2.png') }}" alt="Pet" />
                <img src="{{ url('images/dog3.png') }}" alt="Pet" />
            </div>
            
            <div class="subscription-details">
                <h2>Subscription Details</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Shop:</span>
                    <span class="detail-value">{{ $shop->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Subscription ID:</span>
                    <span class="detail-value">{{ $subscription->id }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">₱{{ number_format($subscription->amount, 2) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Start Date:</span>
                    <span class="detail-value">{{ $subscription->subscription_starts_at ? date('F j, Y', strtotime($subscription->subscription_starts_at)) : 'N/A' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">End Date:</span>
                    <span class="detail-value">{{ date('F j, Y', strtotime($subscription->subscription_ends_at)) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="status-cancelled">Cancelled</span>
                </div>
            </div>
            
            <p>Your access to premium features has been revoked. You can resubscribe at any time to restore these features.</p>
            
            <p style="text-align: center;">
                <a href="{{ route('shop.subscriptions.index') }}" class="button">Manage Subscriptions</a>
            </p>
            
            <p>If you did not request this cancellation, please contact our support team immediately.</p>
            
            <p>Thank you for using PetCareConnect!</p>
        </div>
        
        <div class="email-footer">
            <p>© {{ date('Y') }} PetCareConnect. All rights reserved.</p>
            
            <div class="social-links">
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook" /></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/3670/3670151.png" alt="Instagram" /></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" /></a>
            </div>
            
            <p class="text-muted">If you're having trouble clicking the "Manage Subscriptions" button, copy and paste the URL below into your web browser: {{ route('shop.subscriptions.index') }}</p>
        </div>
    </div>
</body>
</html> 