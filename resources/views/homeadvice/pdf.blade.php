<!DOCTYPE html>
<html>

<head>
    <title>homeadvice Template</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
           .page-border {
            border: 1px solid #a0a0a0;
            padding: 15px;
            box-sizing: border-box;
            height: 993px;
        }


        /* Header */
        .header {
            background-color: #cfece0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
             text-align: center;
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
             margin-bottom: 20px;
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
        .homeadvice-entry {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            width: 100%;
        }

        /* Image */
        .homeadvice-image {
            width: 130px;
            vertical-align: top;
        }

        .homeadvice-image img {
            width: 140px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Text */
        .homeadvice-text {
            padding-left: 15px;
        }

        .homeadvice-title {
            font-size: 20px;
            /* big bold */
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

        .homeadvice-description {
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
            font-size: 20px
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
                            <strong style="font-size: 18px;">{{ $settings['clinic_name'] ?? 'Clinic Name' }}</strong><br>
                            {{ $settings['clinic_phone'] ?? '---' }} ||
                            {{ $settings['clinic_email'] ?? '---' }}
                        </div>
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle;">
                    <div style="line-height: 1.6; font-size: 13px;">
                        <strong style="font-size: 18px; text-transform: uppercase;">HOME ADVICE</strong><br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <!-- Template Name -->
    {{-- <h2 class="template-title">
        {{ $homeadvice->template_name }}
    </h2> --}}

    <div class="section">
        <h3>  {{ $homeadvice->template_name }}</h3>
    </div>


    <!-- Entries -->
    @foreach ($titles as $i => $title)
        <table class="homeadvice-entry">
            <tr>
                <!-- Image -->
                <td class="homeadvice-image">
                    @if (isset($images[$i]) && $images[$i])
                        <img src="{{ public_path($images[$i]) }}" alt="Image">
                    @endif
                </td>

                <!-- Text -->
                <td class="homeadvice-text">
                    <div class="homeadvice-title">{{ ucfirst($title) }}</div>
                    <span class="underline"></span>
                    <div class="homeadvice-description">{{ $descriptions[$i] ?? '' }}</div>
                </td>
            </tr>
        </table>
    @endforeach

    <!-- Footer -->
     <!-- Footer -->
    <div class="container footer">
        <div class="footer-right" width="50%">
            For more information or any issues or concerns,<br>
            email us at <strong>{{ $settings['clinic_email'] ?? 'clinic@example.com' }}</strong> or contact
            <strong>{{ $settings['clinic_phone'] ?? '---' }}</strong>
        </div>
    </div>
     </div>

</body>

</html>
