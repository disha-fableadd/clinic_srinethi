<!DOCTYPE html>
<html>

<head>
    <title>Daily Data Report</title>
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
        .total-amount {
            margin:10px 0;
            text-align: center;
           
        }
    </style>
</head>

<body>
    <div class="page-border">

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
                            <strong style="font-size: 20px; text-transform: uppercase;">Daily Register</strong><br>
                            <strong>Date:</strong> {{ $date }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Patient Details -->
        <div class="section">
            <h3>Patient Details</h3>
            <p><strong>Name:</strong> {{ $daily->patient->fullname ?? '--' }}</p>
            <p><strong>Phone:</strong> {{ $daily->patient->phone ?? '--' }}</p>
        </div>

        <!-- Treatment Details -->
        <div class="section">
            <h3>Treatment Details</h3>
            <p><strong>Treatment:</strong> {{ $daily->treatment->name ?? '--' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($daily->status) }}</p>
            <p><strong>Collected By:</strong> {{ $daily->collectedBy->fullname ?? '--' }}</p>
        </div>

        <!-- Payment Summary -->


        <!-- Payment History -->
        <div class="section">
            <h3>Payment History</h3>

            <div class="total-amount">
              
                    <strong>Total Amount:</strong>
                   {{ optional($daily->paymentHistories->last())->amount ?? '0.00' }}
                

            </div>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment Mode</th>
                        <th>Paid Type</th>
                        <th>Cash</th>
                        <th>Online</th>
                        <th>Paid Amount</th>
                        <th>Remain</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daily->paymentHistories as $p)
                        <tr>
                            <td>{{ $p->created_at->format('d-m-Y h:i A') }}</td>
                            <td>{{ $p->payment_mode ?? '--' }}</td>
                            <td>{{ $p->paid_type ?? '--' }}</td>
                            <td>{{ $p->cash_amount ?? '--' }}</td>
                            <td>{{ $p->online_amount ?? '--' }}</td>
                            <td>{{ $p->paid_amount  ?? '--' }}</td>
                            <td>{{ $p->remain_amount ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No payment history found</td>
                        </tr>
                    @endforelse
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