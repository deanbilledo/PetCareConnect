<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Official Receipt</title>
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
        .payment-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            background-color: #27ae60;
            color: white;
            margin-bottom: 20px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            opacity: 0.05;
            z-index: -1;
            color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL RECEIPT</div>
    <div class="receipt">
        <div class="header">
            @if($appointment->shop->logo)
                <img src="{{ asset('storage/' . $appointment->shop->logo) }}" alt="Shop Logo" class="logo">
            @endif
            <h1 class="receipt-title">{{ $appointment->shop->name }}</h1>
            <p>{{ $appointment->shop->address }}</p>
            <p>Tel: {{ $appointment->shop->phone }}</p>
            <div class="receipt-number">Official Receipt #: OR-{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="payment-status" style="background-color: {{ $appointment->status === 'completed' ? '#27ae60' : '#3498db' }}">
                {{ strtoupper($appointment->status) }}
            </div>
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
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y') }}</p>
                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($appointment->status) }}</p>
                <p><strong>Payment Date:</strong> {{ $appointment->paid_at ? \Carbon\Carbon::parse($appointment->paid_at)->format('F j, Y g:i A') : now()->format('F j, Y g:i A') }}</p>
                @if($appointment->employee)
                <p><strong>Assigned Staff:</strong> {{ $appointment->employee->name }} ({{ $appointment->employee->position }})</p>
                @endif
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
                    @if($appointment->add_ons)
                        @foreach(json_decode($appointment->add_ons) as $addOn)
                            <tr>
                                <td colspan="3">Add-on: {{ $addOn->name }}</td>
                                <td>₱{{ number_format($addOn->price, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="total-section">
            @if($appointment->add_ons_total > 0)
                <p>Service Price: ₱{{ number_format($appointment->service_price - $appointment->add_ons_total, 2) }}</p>
                <p>Add-ons Total: ₱{{ number_format($appointment->add_ons_total, 2) }}</p>
            @endif
            @if($appointment->discount_amount > 0)
                <p>Discount: -₱{{ number_format($appointment->discount_amount, 2) }}</p>
            @endif
            <p class="total-amount">Total Amount Paid: ₱{{ number_format($appointment->service_price, 2) }}</p>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ $appointment->shop->name }}!</p>
            <p>This is an official receipt. Please keep this for your records.</p>
            <p>Date Issued: {{ now()->format('F j, Y g:i A') }}</p>
        </div>
    </div>
</body>
</html> 