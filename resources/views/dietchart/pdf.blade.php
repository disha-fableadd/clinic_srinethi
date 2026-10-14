<!DOCTYPE html>
<html>

<head>
    <title>Diet Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            border: 1px solid #a0a0a0;
            padding: 18px;
            box-sizing: border-box;
        }

        /* Header */
        .header {
            background-color: #cfece0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            width: 90%;
            background-color: #cfece0;
            padding: 10px 20px;
            font-size: 12px;
            color: #000;
            text-align: center;
            border-radius: 5px;
        }

        .footer-right {
            text-align: center;
        }

        /* Template Title */
        .template-title {
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 25px;
        }

        /* Entry Card */
        .diet-entry {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            width: 100%;
        }

        /* Image */
        .diet-image {
            width: 130px;
            vertical-align: top;
        }

        .diet-image img {
            width: 140px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Text */
        .diet-text {
            padding-left: 15px;
        }

        .diet-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .underline {
            display: block;
            width: 50px;
            height: 3px;
            background: #1abc9c;
            margin: 3px 0 8px 0;
            border-radius: 2px;
        }

        .diet-description,
        .diet-time {
            font-size: 13px;
            text-align: justify;
            line-height: 1.5;
        }

        .section {
            margin-top: 15px;
        }

        .section h3 {
            background: #f4f4f4;
            padding: 6px;
            border-left: 4px solid #0c4b33;
            margin-bottom: 10px;
            font-size: 20px;
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
                            @if (!empty($settings['clinic_logo']))
                                <img src="{{ public_path($settings['clinic_logo']) }}" alt="Clinic Logo"
                                    style="width:180px; height:100px; margin-right:12px;">
                            @endif
                            <div style="line-height: 1.4; font-size: 13px;">
                                <strong
                                    style="font-size: 18px;">{{ $settings['clinic_name'] ?? 'Clinic Name' }}</strong><br>
                                {{ $settings['clinic_phone'] ?? '---' }} ||
                                {{ $settings['clinic_email'] ?? '---' }}
                            </div>
                        </div>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: middle;">
                        <div style="line-height: 1.6; font-size: 13px;">
                            <strong style="font-size: 18px; text-transform: uppercase;">DIET CHART</strong><br>
                            <strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>{{ $dietChart->name }}</h3>
        </div>

        <!-- Entries -->
        @foreach ($titles as $i => $title)
            <table class="diet-entry">
                <tr>
                    <!-- Image -->
                    <td class="diet-image">
                        @if (isset($images[$i]) && $images[$i])
                            <img src="{{ public_path('storage/' . $images[$i]) }}" alt="Diet Image">
                        @else
                            <img src="{{ public_path($settings['clinic_logo']) }}" alt="Default Image">
                        @endif
                    </td>

                    <!-- Text -->
                    <td class="diet-text">
                        <div class="diet-title">{{ ucfirst($title) }}</div>
                        <span class="underline"></span>
                        <div class="diet-description">{{ $descriptions[$i] ?? '' }}</div>
                        <div class="diet-time"><strong>Time:</strong> {{ $times[$i] ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        @endforeach

        <!-- Footer -->
        <div class="container footer">
            <div class="footer-right" width="50%">
                For more information or any issues, email at
                <strong>{{ $settings['clinic_email'] ?? 'clinic@example.com' }}</strong>
                or contact <strong>{{ $settings['clinic_phone'] ?? '---' }}</strong>
            </div>
        </div>
    </div>
</body>

</html>
