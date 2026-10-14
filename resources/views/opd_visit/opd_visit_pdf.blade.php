<!DOCTYPE html>
<html>

<head>
    <title>OPD Visit Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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

        .footer {
            position: fixed;
            bottom: 0;
            width: 90%;
            background-color: #cfece0;
            padding: 10px 20px;
            font-size: 12px;
            text-align: center;
            border-radius: 5px;
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
                        <img src="{{ $clinic_logo ?? '' }}" alt="Clinic Logo"
                            style="width:160px; height:90px; margin-right:12px;">
                        <div style="line-height: 1.4; font-size: 13px;">
                            <strong style="font-size: 18px;">{{ $clinic_name ?? 'Clinic Name' }}</strong><br>
                            {{ $clinic_phone ?? '--' }} || {{ $clinic_email ?? 'clinic@example.com' }}
                        </div>
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle;">
                    <div style="line-height: 1.6; font-size: 13px;">
                        <strong style="font-size: 20px; text-transform: uppercase;">OPD VISIT</strong><br>
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

    <!-- Visit Info -->
    <div class="section">
        <h3>Visit Details</h3>
        <p><strong>Doctor:</strong> {{ $doctor->fullname ?? '--' }}</p>
        <p><strong>Visit Date:</strong> {{ $opdVisit->visit_date->format('d-m-Y') ?? '--' }}</p>
        <p><strong>Chief Complaint:</strong> {{ $opdVisit->chief_complaint ?? '--' }}</p>
        <p><strong>Diagnosis:</strong> {{ $opdVisit->diagnosis ?? '--' }}</p>
        <p><strong>Prescription:</strong> {{ $opdVisit->prescription ?? '--' }}</p>
        <p><strong>Consultation Fees:</strong> {{ $opdVisit->consultation_fees ?? '0.00' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($opdVisit->status ?? '--') }}</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-right">
            For more information or any concerns,<br>
            email us at <strong>{{ $clinic_email ?? 'clinic@example.com' }}</strong> or contact
            <strong>{{ $clinic_phone ?? '9876543210' }}</strong>
        </div>
    </div>
</body>

</html>