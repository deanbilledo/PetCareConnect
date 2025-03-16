<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment {{ ucfirst($status) }}</title>
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
        .payment-details {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .payment-details h2 {
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
        .status-verified {
            display: inline-block;
            background-color: #DEF7EC;
            color: #03543E;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-rejected {
            display: inline-block;
            background-color: #FEE2E2;
            color: #B91C1C;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-pending {
            display: inline-block;
            background-color: #FEF3C7;
            color: #92400E;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .receipt-image {
            margin-top: 15px;
            text-align: center;
        }
        .receipt-image img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <img src="{{ url('images/logo.png') }}" alt="PetCareConnect" />
        </div>
        
        <div class="email-body">
            <h1>Payment {{ ucfirst($status) }}</h1>
            
            <p>Hello {{ $user->name }},</p>
            
            @if($status == 'verified')
                <p>Your payment for <strong>{{ $shop->name }}</strong> subscription has been <strong>verified</strong>. Your subscription is now active.</p>
            @elseif($status == 'rejected')
                <p>Your payment for <strong>{{ $shop->name }}</strong> subscription has been <strong>rejected</strong>. Please submit a new payment.</p>
            @else
                <p>Your payment for <strong>{{ $shop->name }}</strong> subscription is being processed.</p>
            @endif
            
            <div class="pet-images">
                <img src="{{ url('images/dog1.png') }}" alt="Pet" />
                <img src="{{ url('images/dog2.png') }}" alt="Pet" />
                <img src="{{ url('images/dog3.png') }}" alt="Pet" />
            </div>
            
            <div class="payment-details">
                <h2>Payment Details</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Shop Name:</span>
                    <span class="detail-value">{{ $shop->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Reference Number:</span>
                    <span class="detail-value">{{ $subscription->reference_number ?? 'N/A' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">₱{{ number_format($subscription->amount, 2) }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Date:</span>
                    <span class="detail-value">{{ $subscription->updated_at ? date('F j, Y', strtotime($subscription->updated_at)) : date('F j, Y') }}</span>
                </div>
                
                @if($status == 'verified')
                <div class="detail-row">
                    <span class="detail-label">Subscription Start:</span>
                    <span class="detail-value">{{ $subscription->subscription_starts_at ? date('F j, Y', strtotime($subscription->subscription_starts_at)) : date('F j, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Subscription End:</span>
                    <span class="detail-value">{{ $subscription->subscription_ends_at ? date('F j, Y', strtotime($subscription->subscription_ends_at)) : date('F j, Y', strtotime('+30 days')) }}</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    @if($status == 'verified')
                        <span class="status-verified">Verified</span>
                    @elseif($status == 'rejected')
                        <span class="status-rejected">Rejected</span>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </div>
            </div>
            
            @if($subscription->payment_screenshot && $status != 'rejected')
            <div class="receipt-image">
                <h3>Payment Receipt</h3>
                <img src="{{ asset('storage/' . $subscription->payment_screenshot) }}" alt="Payment Receipt">
            </div>
            @endif
            
            @if($status == 'verified')
                <p>Your subscription is now active until {{ $subscription->subscription_ends_at ? date('F j, Y', strtotime($subscription->subscription_ends_at)) : date('F j, Y', strtotime('+30 days')) }}. You can now access all premium features of PetCareConnect.</p>
            @elseif($status == 'rejected')
                <p>Your payment has been rejected. This may be due to incomplete information or payment verification issues. Please submit a new payment to activate your subscription.</p>
                <p>If you believe this is an error, please contact our support team for assistance.</p>
            @else
                <p>Your payment is being processed. We will notify you once it has been verified.</p>
            @endif
            
            <p style="text-align: center;">
                <a href="{{ route('shop.subscriptions.index') }}" class="button">Manage Subscription</a>
            </p>
            
            <p>Thank you for using PetCareConnect!</p>
        </div>
        
        <div class="email-footer">
            <p>© {{ date('Y') }} PetCareConnect. All rights reserved.</p>
            
            <div class="social-links">
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook" /></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/3670/3670151.png" alt="Instagram" /></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" /></a>
            </div>
            
            <p class="text-muted">If you're having trouble clicking the "Manage Subscription" button, copy and paste the URL below into your web browser: {{ route('shop.subscriptions.index') }}</p>
        </div>
    </div>
</body>
</html> 