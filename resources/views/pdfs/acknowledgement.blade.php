<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Acknowledgement</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .shop-name {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .details {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .detail-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .total {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .notice {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #000;
            background-color: #f8f8f8;
        }
        @page {
            margin: 0.5cm 2cm;
        }
        .logo {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="Company Logo">
    </div>
    <div class="header">
        <div class="title">APPOINTMENT ACKNOWLEDGEMENT</div>
        <div class="shop-name">{{ $booking_details['shop_name'] }}</div>
    </div>

    <div class="details">
        <div class="detail-row">
            <span class="label">Reference No:</span>
            <span>ACK-{{ date('Ymd') }}-{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Date:</span>
            <span>{{ $booking_details['date'] }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Time:</span>
            <span>{{ $booking_details['time'] }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="detail-row">
            <span class="label">Name:</span>
            <span>{{ auth()->user()->name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Email:</span>
            <span>{{ auth()->user()->email }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Services Booked</div>
        @foreach($booking_details['services'] as $service)
        <div class="detail-row">
            <div style="margin-bottom: 10px;">
                <strong>Service:</strong> {{ $service['service_name'] }}<br>
                <strong>Pet:</strong> {{ $service['pet_name'] }} ({{ ucfirst($service['size']) }})<br>
                <strong>Price:</strong> ₱{{ number_format($service['price'], 2) }}
            </div>
        </div>
        @endforeach

        <div class="total">
            <span class="label">Total Amount:</span>
            <span>₱{{ number_format($booking_details['total_amount'], 2) }}</span>
        </div>
    </div>

    <div class="notice">
        <strong>Important Notice:</strong>
        <ul style="margin-top: 10px;">
            <li>This is an acknowledgement of your appointment booking request.</li>
            <li>The shop will review your request and confirm the appointment.</li>
            <li>You will receive a confirmation email once the shop accepts your appointment.</li>
            <li>Please wait for the confirmation before visiting the shop.</li>
        </ul>
    </div>

    <div class="footer">
        <p>Thank you for choosing {{ $booking_details['shop_name'] }}!</p>
        <p>This is a computer-generated acknowledgement. No signature required.</p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html> 