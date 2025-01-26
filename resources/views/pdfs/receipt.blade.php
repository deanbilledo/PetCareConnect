<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-number {
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 30px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
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
                <th>Customer Name:</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Appointment Date:</th>
                <td>{{ $booking_details['date'] }}</td>
            </tr>
            <tr>
                <th>Appointment Time:</th>
                <td>{{ $booking_details['time'] }}</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>Pending</td>
            </tr>
        </table>

        <div class="total">
            <p>Total Amount: ₱{{ number_format($booking_details['total_amount'], 2) }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing {{ $shop->name }}!</p>
        <p>This is a computer-generated receipt. No signature required.</p>
    </div>
</body>
</html> 