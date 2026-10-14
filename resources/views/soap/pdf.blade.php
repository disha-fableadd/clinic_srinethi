<!DOCTYPE html>
<html>

<head>
    <title>SOAP Report</title>
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

        .clinic-details {
            font-size: 12px;
            padding-left: 8px;
        }

        h2,
        h3 {
            margin-bottom: 3px;
        }


        /* p {
            margin: 1px 0;
        } */


        .footer {
            position: fixed;
            bottom: 0;
            width: 90%;
            background-color: #cfece0;
            padding: 10px 20px;
            font-size: 12px;
            color: #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
             margin-bottom: 20px;
 
        }

        .footer-right {
            text-align: center;
        }

        .section {
            margin-top: 20px;
        }

        .section h3 {
            background: #f4f4f4;
            padding: 8px;
            border-left: 4px solid #0c4b33;
            margin-bottom: 8px;
        }

        .section p {
            padding: 4px 8px;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    <div class="page-border">

    <!-- Header -->

    <!-- <div class="header">
        <table width="100%">
            <tr>
                <td width="70%" class="text-center">
                    <div>
                        <img src="{{ $clinic_logo ?? 'clinic logo' }}" class="clinic-logo" alt="Logo"><br>
                        <strong>{{ $clinic_name ?? 'Clinic Name' }}</strong><br>
                        {{ $clinic_phone ?? '--' }}<br>
                        {{ $clinic_email ?? 'clinic@example.com' }}
                    </div>
                </td>
                <td class="text-right" width="30%">
                    <h2>SOAP Report</h2>
                    <p><strong>Date:</strong> {{ $date }}</p>
                </td>
            </tr>
        </table>
    </div> -->


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
                        <strong style="font-size: 20px; text-transform: uppercase;">SOAP</strong><br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Patient Info -->

   <!-- Patient Info -->
   <div  class="section" >
        <h3>Patient Details</h3>
        <p><strong>Name:</strong> {{ $patient->fullname ?? '--' }}</p>

        <p><strong>Phone:</strong> {{ $patient->phone ?? '--' }}</p>
        <p><strong>Address:</strong> {{ $patient->address ?? '--' }}</p>
    </div>



    <!-- SOAP Sections -->
    <div class="section">
        <h3>Subjective: {{ $subjective['title'] }}</h3>
        <p>{{ $subjective['description'] }}</p>

    </div>

    <div class="section">
        <h3>Objective: {{ $objective['title'] }}</h3>
        <p>{{ $objective['description'] }}</p>
    </div>

    <div class="section">
        <h3>Assessment: {{ $assessment['title'] }}</h3>
        <p>{{ $assessment['description'] }}</p>
    </div>

    <div class="section">
        <h3>Plan: {{ $plan['title'] }}</h3>
        <p>{{ $plan['description'] }}</p>
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