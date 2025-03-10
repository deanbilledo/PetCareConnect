<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Analytics Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #2563EB;
        }
        .header p {
            font-size: 14px;
            color: #666;
            margin-top: 0;
        }
        .shop-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #F3F4F6;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.summary td {
            padding: 8px 10px;
        }
        table.summary tr td:first-child {
            font-weight: bold;
            width: 40%;
        }
        table.data {
            border: 1px solid #ddd;
        }
        table.data th {
            background-color: #2563EB;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 10px;
        }
        table.data td {
            border-top: 1px solid #ddd;
            padding: 8px 10px;
        }
        table.data tr:nth-child(even) {
            background-color: #F3F4F6;
        }
        h2 {
            color: #2563EB;
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Shop Analytics Report</h1>
        <p>Generated on {{ $data['generated_at'] }}</p>
    </div>

    <div class="shop-info">
        <table>
            <tr>
                <td><strong>Shop Name:</strong></td>
                <td>{{ $data['shop_name'] }}</td>
                <td><strong>Shop Type:</strong></td>
                <td>{{ $data['shop_type'] }}</td>
            </tr>
        </table>
    </div>

    <h2>Summary</h2>
    <table class="summary">
        <tr>
            <td>Total Appointments:</td>
            <td>{{ $data['summary']['total_appointments'] }}</td>
        </tr>
        <tr>
            <td>Total Revenue:</td>
            <td>{{ $data['summary']['total_revenue'] }}</td>
        </tr>
        <tr>
            <td>Total Customers:</td>
            <td>{{ $data['summary']['total_customers'] }}</td>
        </tr>
        <tr>
            <td>Active Services:</td>
            <td>{{ $data['summary']['active_services'] }}</td>
        </tr>
    </table>

    <h2>Monthly Revenue</h2>
    <table class="data">
        <tr>
            <th>Month</th>
            <th class="text-right">Amount (PHP)</th>
        </tr>
        @foreach($data['monthly_revenue'] as $monthData)
        <tr>
            <td>{{ $monthData['month'] }}</td>
            <td class="text-right">₱{{ number_format($monthData['total'], 2) }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Paid Appointments</h2>
    <table class="data">
        <tr>
            <th>Customer</th>
            <th>Pet</th>
            <th>Service</th>
            <th>Employee</th>
            <th>Date</th>
            <th class="text-right">Amount</th>
        </tr>
        @foreach($data['paid_appointments'] as $appointment)
        <tr>
            <td>{{ $appointment['customer'] }}</td>
            <td>{{ $appointment['pet'] }}</td>
            <td>{{ $appointment['service'] }}</td>
            <td>{{ $appointment['employee'] }}</td>
            <td>{{ $appointment['date'] }}</td>
            <td class="text-right">{{ $appointment['amount'] }}</td>
        </tr>
        @endforeach
    </table>

    <div style="text-align: center; margin-top: 50px; font-size: 10px; color: #666;">
        <p>Pet Care Connect Analytics Report &copy; {{ date('Y') }}</p>
    </div>
</body>
</html> 