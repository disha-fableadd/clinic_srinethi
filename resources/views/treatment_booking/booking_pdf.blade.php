<!DOCTYPE html>
<html>

<head>
    <title>Treatment Booking Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .page-border {
            border: 1px solid #a0a0a0;
            padding: 15px;
            box-sizing: border-box;
            height: 993px;
        }

        .header {
            background-color: #cfece0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .section {
            margin-top: 15px;
        }

        .section h3 {
            background: #f4f4f4;
            padding: 6px;
            border-left: 4px solid #0c4b33;
            margin-bottom: 6px;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .invoice-box th,
        .invoice-box td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .invoice-box thead tr {
            background-color: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 90%;
            background-color: #cfece0;
            padding: 10px 20px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            margin-bottom: 20px
        }

        .footer-right {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="page-border">


        <!-- Header -->
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 70%; vertical-align: middle;">
                        <div style="display: flex; align-items: center;">
                            <img src="{{ $clinic_logo ?? 'clinic logo' }}" alt="Clinic Logo"
                                style="width:180px; height:100px; margin-right:12px;">
                            <div style="line-height: 1.4; font-size: 13px;">
                                <strong style="font-size: 18px;">{{ $clinic_name ?? 'Clinic Name' }}</strong><br>
                                {{ $clinic_phone ?? '--' }} || {{ $clinic_email ?? 'clinic@example.com' }}
                            </div>
                        </div>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: middle;">
                        <div style="line-height: 1.6; font-size: 13px;">
                            <strong style="font-size: 20px; text-transform: uppercase;">TREATMENT BOOKING</strong><br>
                            <strong>Date:</strong> {{ $date }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Patient Info -->
        <div class="section">
            <h3>Patient Details</h3>
            <p><strong>Name:</strong> {{ $patient->fullname ?? '--' }}</p>
            <p><strong>Phone:</strong> {{ $patient->phone ?? '--' }}</p>
            <p><strong>Address:</strong> {{ $patient->address ?? '--' }}</p>
        </div>

        <!-- Booking Info -->
        <div class="section">
            <h3>Treatment Booking Details</h3>
            <p><strong>Treatment:</strong> {{ $treatment->name ?? '--' }}</p>
            <p><strong>Plan:</strong> {{ $booking->plan ?? '--' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($booking->status) ?? '--' }}</p>
            <p><strong>Remain Amount:</strong> {{ $booking->remain_amount ?? '0.00' }}</p>
            <p><strong>Payment Date:</strong> {{ $booking->payment_date ?? '--' }}</p>
        </div>

        <!-- Payment History -->
        <div class="section">
            <h3>Payment History</h3>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Paid Type</th>
                        <th>Cash</th>
                        <th>Online</th>
                        <th>Remain</th>

                    </tr>
                </thead>
                <tbody>
                    @if($history->isNotEmpty())
                        @foreach($history as $h)
                            <tr>
                                <td>{{ $h->created_at->format('d-m-Y h:i A') ?? '--' }}</td>
                                <td>{{ $h->amount ?? '0.00' }}</td>
                                <td>{{ $h->payment_mode ?? '--' }}</td>
                                <td>{{ $h->paid_type ?? '--' }}</td>
                                <td>{{ $h->cash ?? '0.00' }}</td>
                                <td>{{ $h->online ?? '0.00' }}</td>
                                <td>{{ $h->remain_amount ?? '0.00' }}</td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">No payment history found.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-right">
                For more information or any concerns,<br>
                email us at <strong>{{ $clinic_email ?? 'clinic@example.com' }}</strong> or contact
                <strong>{{ $clinic_phone ?? '9876543210' }}</strong>
            </div>
        </div>
    </div>

</body>

</html>