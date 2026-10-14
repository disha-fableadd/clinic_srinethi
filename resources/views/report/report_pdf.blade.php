<!DOCTYPE html>
<html>

<head>
    <title>Medical Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* margin: 20px; */
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
        }

        .footer-right {
            text-align: center;
        }
    </style>
</head>

<body>

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
                        <strong style="font-size: 20px; text-transform: uppercase;">MEDICAL REPORT</strong><br>
                        <strong>Date:</strong> {{ $date }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Patient Info -->
  <div  class="section" >
        <h3>Patient Details</h3>
        <p><strong>Name:</strong> {{ $patient->fullname ?? '--' }}</p>
        <p><strong>Phone:</strong> {{ $patient->phone ?? '--' }}</p>
        <p><strong>Address:</strong> {{ $patient->address ?? '--' }}</p>
    </div>

    <!-- Report Info -->
    <div class="section">
        <h3>Report Details</h3>
        <p><strong>Type:</strong> {{ ucfirst($report->report_type) ?? '--' }}</p>
        <p><strong>Date:</strong> {{ $report->date ?? '--' }}</p>
        <p><strong>Description:</strong> {{ $report->description ?? '--' }}</p>
        @if($report->file_path)
            <p><strong>Attached File:</strong></p>
            <img src="{{ public_path('uploads/report/' . basename($report->file_path)) }}" alt="Report File"
                style="max-width:100%; max-height:400px; margin-top:10px;">
        @endif

    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-right">
            For more information or concerns,<br>
            email us at <strong>{{ $clinic_email ?? 'clinic@example.com' }}</strong> or contact
            <strong>{{ $clinic_phone ?? '9876543210' }}</strong>
        </div>
    </div>

</body>

</html>