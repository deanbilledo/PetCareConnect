<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acknowledgment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .receipt-number {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-block {
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 5px;
        }
        .info-block h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .info-block p {
            margin: 5px 0;
            color: #34495e;
            font-size: 14px;
        }
        .service-details {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            background-color: #3498db;
            color: white;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            @if($shop->logo)
                <img src="{{ asset('storage/' . $shop->logo) }}" alt="Shop Logo" class="logo">
            @endif
            <h1 class="receipt-title">{{ $shop->name }}</h1>
            <p>{{ $shop->address }}</p>
            <p>Tel: {{ $shop->phone }}</p>
            <div class="receipt-number">Receipt #: ACK-{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="status">ACKNOWLEDGMENT</div>
        </div>

        <div class="info-grid">
            <div class="info-block">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> {{ $appointment->user->name }}</p>
                <p><strong>Email:</strong> {{ $appointment->user->email }}</p>
                <p><strong>Phone:</strong> {{ $appointment->user->phone }}</p>
            </div>
            <div class="info-block">
                <h3>Appointment Details</h3>
                <p><strong>Date:</strong> {{ $appointment->appointment_date->format('F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $appointment->appointment_date->format('g:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>
            </div>
        </div>

        <div class="service-details">
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Pet</th>
                        <th>Duration</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $appointment->service_type }}</td>
                        <td>{{ $appointment->pet->name }} ({{ $appointment->pet->breed }})</td>
                        <td>1 hour</td>
                        <td>₱{{ number_format($appointment->service_price, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <p class="total-amount">Total Amount: ₱{{ number_format($appointment->service_price, 2) }}</p>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ $shop->name }}!</p>
            <p>This is an acknowledgment receipt. Please keep this for your records.</p>
            <p>Date Issued: {{ now()->format('F j, Y g:i A') }}</p>
        </div>
    </div>
</body>
</html> 