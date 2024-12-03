<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Booking Receipt</title>
    <style>
        @font-face {
            font-family: 'Arial Unicode MS';
            font-style: normal;
            font-weight: normal;
            src: url('fonts/arial-unicode-ms.ttf') format('truetype');
        }
        body {
            font-family: 'Arial Unicode MS', Arial, sans-serif;
            margin: 0;
            padding: 40px;
            color: #333;
            line-height: 1.6;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 150px;
            height: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #2563eb;
            margin: 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .receipt-number {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 8px;
        }
        .receipt-number h2 {
            color: #1e40af;
            margin: 0;
            font-size: 18px;
        }
        .receipt-number p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .details {
            margin-bottom: 30px;
        }
        .details h3 {
            color: #1e40af;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details th, .details td {
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .details th {
            width: 35%;
            color: #666;
            font-weight: normal;
        }
        .details td {
            color: #333;
            font-weight: 500;
        }
        .services {
            margin: 20px 0;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
        }
        .services h3 {
            margin-top: 0;
            color: #1e40af;
            font-size: 16px;
        }
        .service-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .service-item:last-child {
            border-bottom: none;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .total p {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin: 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: #fef3c7;
            color: #92400e;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="logo">
        <!-- Base64 encoded logo -->
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" alt="Logo">
    </div>

    <div class="header">
        <h1>{{ $shop->name }}</h1>
        <p>{{ $shop->address }}</p>
        <p>Phone: {{ $shop->phone }}</p>
    </div>

    <div class="receipt-number">
        <h2>Receipt #{{ $receipt_number }}</h2>
        <p>Date: {{ date('F j, Y') }}</p>
    </div>

    <div class="details">
        <h3>Appointment Details</h3>
        <table>
            <tr>
                <th>Customer Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Appointment Date</th>
                <td>{{ $booking_details['date'] }}</td>
            </tr>
            <tr>
                <th>Appointment Time</th>
                <td>{{ $booking_details['time'] }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="status-badge">Pending</span></td>
            </tr>
        </table>

        <div class="services">
            <h3>Services Breakdown</h3>
            @foreach($booking_details['services'] as $service)
                <div class="service-item">
                    <span>
                        {{ $service['pet_name'] }} - {{ $service['service_name'] }}
                        <span style="color: #666;">({{ ucfirst($service['size']) }})</span>
                    </span>
                    <span>&#8369;{{ number_format($service['price'], 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="total">
            <p>Total Amount: &#8369;{{ number_format($booking_details['total_amount'], 2) }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing {{ $shop->name }}!</p>
        <p>This is a computer-generated receipt. No signature required.</p>
        <p style="color: #999; margin-top: 10px;">Generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html> 