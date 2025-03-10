<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pet Care Connect - Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .summary {
            margin-top: 30px;
        }
        .summary-item {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Pet Care Connect Logo" class="logo">
        <h1>Reports and Analytics</h1>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-item">
            <strong>Total Appointments:</strong> {{ $appointments->count() }}
        </div>
        <div class="summary-item">
            <strong>Total Revenue:</strong> ₱{{ number_format($appointments->sum('total_amount'), 2) }}
        </div>
        <div class="summary-item">
            <strong>Completed Appointments:</strong> {{ $appointments->where('status', 'completed')->count() }}
        </div>
        <div class="summary-item">
            <strong>Cancelled Appointments:</strong> {{ $appointments->where('status', 'cancelled')->count() }}
        </div>
    </div>

    <h2>Detailed Report</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Shop</th>
                <th>Customer</th>
                <th>Services</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->created_at->format('Y-m-d') }}</td>
                    <td>{{ $appointment->shop->name }}</td>
                    <td>{{ $appointment->user->name }}</td>
                    <td>{{ $appointment->services->pluck('name')->implode(', ') }}</td>
                    <td>{{ ucfirst($appointment->status) }}</td>
                    <td>₱{{ number_format($appointment->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report is generated automatically by Pet Care Connect Platform.</p>
        <p>© {{ date('Y') }} Pet Care Connect. All rights reserved.</p>
    </div>
</body>
</html> 