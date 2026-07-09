<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Contribution Receipt') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3c8dbc;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .success-badge {
            text-align: center;
            margin-bottom: 25px;
        }
        .success-badge .icon {
            font-size: 40px;
            color: #2ecc71;
            margin-bottom: 5px;
        }
        .success-badge h2 {
            margin: 5px 0 0;
            font-size: 28px;
            color: #2c3e50;
        }
        .success-badge .date {
            font-size: 13px;
            color: #95a5a6;
            margin-top: 5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th, .details-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f2f2f2;
        }
        .details-table th {
            color: #7f8c8d;
            font-weight: 600;
            width: 35%;
        }
        .details-table td {
            color: #2c3e50;
            font-weight: 500;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #bdc3c7;
            border-top: 1px solid #f2f2f2;
            padding-top: 15px;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>MANZESE SDA CHURCH</h1>
            <p>{{ __('Giving & Finance Department') }}</p>
        </div>

        <div class="success-badge">
            <div class="icon">&#10004;</div>
            <h2>{{ $currencySymbol }} {{ number_format($contribution->amount, 2) }}</h2>
            <div class="date">{{ __('Recorded on') }} {{ $contribution->created_at->format('M d, Y h:i A') }}</div>
        </div>

        <table class="details-table">
            <tr>
                <th>{{ __('Member') }}</th>
                <td>{{ $contribution->member->full_name }}</td>
            </tr>
            <tr>
                <th>{{ __('Type') }}</th>
                <td>
                    @php
                        $typeLabel = match($contribution->type) {
                            'zaka' => __('Zaka (Tithe)'),
                            'sadaka' => __('Sadaka (Offering)'),
                            'building' => __('Building Fund'),
                            'thanksgiving' => __('Thanksgiving'),
                            'project' => __('Project'),
                            default => ucfirst($contribution->type),
                        };
                    @endphp
                    {{ $typeLabel }}
                </td>
            </tr>
            <tr>
                <th>{{ __('Date') }}</th>
                <td>{{ $contribution->date->format('M d, Y') }}</td>
            </tr>
            <tr>
                <th>{{ __('Payment Method') }}</th>
                <td>{{ ucfirst($contribution->payment_method) }}</td>
            </tr>
            @if($contribution->reference_number)
                <tr>
                    <th>{{ __('Reference No.') }}</th>
                    <td>{{ $contribution->reference_number }}</td>
                </tr>
            @endif
            @if($contribution->notes)
                <tr>
                    <th>{{ __('Notes') }}</th>
                    <td>{{ $contribution->notes }}</td>
                </tr>
            @endif
        </table>

        <div class="footer">
            <p>{{ __('Thank you for supporting the work of the Lord.') }}</p>
            <p>&copy; {{ date('Y') }} Manzese SDA Church. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
