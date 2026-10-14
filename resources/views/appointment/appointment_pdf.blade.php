<!DOCTYPE html>
<html>

<head>
    <title>Appointment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* margin: 20px; */
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
            margin-bottom: 20px;
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
                        <strong style="font-size: 20px; text-transform: uppercase;">APPOINTMENT</strong><br>
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
    <!-- Appointment Info -->
    <div class="section">
        <h3>Appointment Details</h3>
        @php
            $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
        @endphp
        <p><strong>Doctor:</strong> {{ $doctor->fullname ?? '--' }}</p>
        <p><strong>Treatment:</strong> {{ $treatment->name ?? '--' }}</p>
        <p><strong>Date:</strong> {{ $appointment->date ?? '--' }}</p>
        @if($currentProjectTypeId !== 3)
        <p><strong>Status:</strong> {{ ucfirst($appointment->status) ?? '--' }}</p>
        @endif
        <p><strong>Duration:</strong> {{ $appointment->duration ?? '--' }} mins</p>
        @if($currentProjectTypeId !== 3)
        <p><strong>Clinic Location:</strong> {{ $appointment->clinic_location ?? '--' }}</p>
        @endif
    </div>

    <!-- Appointment History -->
    <div class="section">
        <h3>Appointment History</h3>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    @if($currentProjectTypeId !== 3)
                    <th>Status</th>
                    @endif
                    <th>Comment</th>

                </tr>
            </thead>
            <tbody style="text-align: center;">
                @if($history->isNotEmpty())
                    @foreach($history as $h)
                        <tr>
                            <td>{{ $h->date ?? '--' }}</td>
                            <td>{{ $h->time ?? '--' }}</td>
                            @if($currentProjectTypeId !== 3)
                            <td>{{ ucfirst($h->status) ?? '--' }}</td>
                            @endif
                            <td>{{ $h->comment ?? '--' }}</td>

                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No history found.</td>
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