<!DOCTYPE html>
<html>
<head>
    <title>Doctor Appointment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background:#cfece0;
            color: Black;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .content {
            padding: 20px;
            color: #333333;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666666;
            padding-top: 10px;
            border-top: 1px solid #dddddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Doctor Appointment Reminder</div>
        <div class="content">
            <p>Dear Dr. {{  $recipient  }},</p>
            <p>This is a reminder of your scheduled patient appointments for the day.</p>
            <p>
                <strong>Patient Name:</strong> {{  $recipient  }}<br>
                <strong>Date:</strong> {{ $date }}<br>
                <strong>Time:</strong> {{ $time }}<br>
             
            </p>
            <p>Please ensure you review the patient's history and be prepared for the consultation.</p>
            <p>For any scheduling adjustments, please contact the clinic administration.</p>
        </div>
        <div class="footer">
            &copy; 2025 XYZ Clinic. All rights reserved.
        </div>
    </div>
</body>
</html>
