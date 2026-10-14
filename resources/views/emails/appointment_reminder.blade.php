<!DOCTYPE html>
<html>
<head>
    <title>Appointment Reminder</title>
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
        <div class="header">Appointment Reminder</div>
        <div class="content">
            <p>Dear {{ $recipient }},</p>
            <p>This is a friendly reminder for your upcoming appointment. Please find the details below:</p>
            <p>
                <strong>Date:</strong> {{ $date }}<br>
                <strong>Time:</strong> {{ $time }}<br>
                <strong>Location:</strong> {{ $clinic_location }}
            </p>
       
            <p>We look forward to seeing you soon!</p>
        </div>
        <div class="footer">
            &copy; 2025 XYZ Clinic. All rights reserved.
        </div>
    </div>
</body>
</html>
