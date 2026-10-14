<!DOCTYPE html>
<html>

<head>
    <title>Patient Medicines Report</title>
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

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .clinic-logo {
            max-width: 40px;
            max-height: 40px;
        }

        h2,
        h3 {
            margin-bottom: 5px;
        }
/* 
        p {
            margin: 2px 0;
        } */

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

        .section {
            margin-top: 15px;
        }

        .section h3 {
            background: #f4f4f4;
            padding: 6px;
            border-left: 4px solid #0c4b33;
            margin-bottom: 6px;
        }

        .section p {
            padding: 4px 8px;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 13px;
        }

        .invoice-box th,
        .invoice-box td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .invoice-box thead tr {
            background-color: #f2f2f2;
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
                            {{ $clinic_phone ?? '--' }} ||
                            {{ $clinic_email ?? 'clinic@example.com' }}
                        </div>
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle;">
                    <div style="line-height: 1.6; font-size: 13px;">
                        <strong style="font-size: 20px; text-transform: uppercase;">ASSESSMENT</strong><br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}
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

    <!-- Treatment Info -->
    <div class="section">
        <h3>Treatment Details</h3>
        <p><strong>Treatment Name:</strong> {{ $treatment->name ?? '--' }}</p>
        <p><strong>Description:</strong> {{ $treatment->description ?? '--' }}</p>
        <p><strong>Doctor:</strong> {{ $treatment->doctor->fullname ?? '--' }}</p>
    </div>


    <!-- Medicines -->
    <div class="section">
        <h3 >Prescribed Medicines</h3>
        <table class="invoice-box">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Code</th>
                    <th>Value</th>
                    <th>Value Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($medicines) && count($medicines) > 0)
                    @foreach($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine['name'] ?? '--' }}</td>
                            <td>{{ $medicine['code'] ?? '--' }}</td>
                            <td>{{ $medicine['value'] ?? '--' }}</td>
                            <td>{{ $medicine['value_type'] ?? '--' }}</td>
                            <td>{{ $medicine['description'] ?? '--' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">No medicines found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>


    <div class="section">
        <h3>Note</h3>
        <p><strong>Note:</strong> {{ $note ?? '--' }}</p>
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
        </div>


</body>

</html>