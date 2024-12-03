<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 10px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .shop-name {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .detail-row {
            margin-bottom: 3px;
            line-height: 1.3;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .total {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #000;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Company Logo">
        <div class="receipt-title">OFFICIAL RECEIPT</div>
        <div class="shop-name">{{ $shop->name ?? 'Shop Name Not Available' }}</div>
        <div>{{ $shop->address ?? 'Address Not Available' }}</div>
    </div>

    <div class="receipt-details">
        <div class="detail-row">
            <span class="label">Receipt No:</span>
            <span>RCP-{{ $appointment->id }}-{{ date('Ymd') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Date:</span>
            <span>{{ $appointment->appointment_date->format('F d, Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Time:</span>
            <span>{{ $appointment->appointment_date->format('h:i A') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="detail-row">
            <span class="label">Name:</span>
            <span>{{ $user->name ?? 'Name Not Available' }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Email:</span>
            <span>{{ $user->email ?? 'Email Not Available' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Pet Information</div>
        <div class="detail-row">
            <span class="label">Name:</span>
            <span>{{ $pet->name ?? 'Name Not Available' }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Type:</span>
            <span>{{ $pet->type ?? 'Type Not Available' }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Size:</span>
            <span>{{ $pet->size_category ? ucfirst($pet->size_category) : 'Size Not Available' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Service Details</div>
        <div class="detail-row">
            <span class="label">Service:</span>
            <span>{{ $appointment->service_type ?? 'Service Not Available' }}</span>
        </div>
        @if($service)
        <div class="detail-row">
            <span class="label">Duration:</span>
            <span>{{ $service->duration ?? 'N/A' }} minutes</span>
        </div>
        <div class="detail-row">
            <span class="label">Base Price:</span>
            <span>₱{{ number_format($service->base_price ?? 0, 2) }}</span>
        </div>
        @if(($pet->size_category ?? 'small') !== 'small')
        <div class="detail-row">
            <span class="label">Size Adjustment:</span>
            <span>{{ ($pet->size_category ?? 'small') === 'large' ? '+40%' : '+20%' }}</span>
        </div>
        @endif
        @endif
        <div class="total">
            <span class="label">Total Amount:</span>
            <span>₱{{ number_format($service ? $service->getPriceForSize($pet->size_category ?? 'small') : ($appointment->service_price ?? 0), 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing {{ $shop->name ?? 'our services' }}!</p>
        <p>This is a computer-generated receipt. No signature required.</p>
    </div>
</body>
</html> 