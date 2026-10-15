<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointment</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #222;
            background-color: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .invoice-wrapper {
            width: 190mm;
            height: 277mm;
            margin: 10mm auto 0;
            padding: 12px 14px;
            background-color: #fff;
            border: 1.5px solid #222;
            box-sizing: border-box;
            overflow: hidden;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .clinic-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a4d36;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .clinic-sub {
            font-size: 11px;
            color: #444;
            line-height: 1.35;
        }

        .header-divider {
            border: none;
            border-top: 1.5px solid #222;
            margin: 12px 0 14px 0;
        }

        .details-container {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d0e4dc;
            background-color: #f7faf8;
        }

        .details-box {
            padding: 9px 12px;
            vertical-align: top;
        }

        .details-box-left {
            width: 50%;
            border-right: 1px solid #d0e4dc;
        }

        .details-box-right {
            width: 50%;
        }

        .section-heading {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
            font-weight: bold;
            color: #0c4b33;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px dashed #c4ddd1;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
        }

        .info-table td,
        .info-table th {
            padding: 2.5px 0;
            vertical-align: top;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11.5px;
        }

        .info-label {
            width: 100px;
            color: #4b5563;
            font-weight: normal;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .info-sep {
            width: 12px;
            color: #4b5563;
            text-align: center;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .info-val {
            color: #1f2937;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .section-banner {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            padding: 5px 0;
            letter-spacing: 1px;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
        }

        .invoice-box th {
            background-color: #cfece0;
            color: #0c4b33;
            font-weight: bold;
            text-align: center;
            padding: 6px 8px;
            border: 1px solid #b8dfd0;
            font-size: 12px;
            letter-spacing: 0.3px;
            vertical-align: middle;
        }

        .invoice-box td {
            padding: 6px 8px;
            border: 1px solid #d5e5dd;
            color: #333;
            font-size: 12px;
            vertical-align: middle;
            text-align: center;
        }

        .invoice-box tbody tr:nth-child(even) {
            background-color: #fbfdfc;
        }

        .invoice-box tfoot td {
            padding: 5px 8px;
            border: 1px solid #d5e5dd;
            font-size: 12px;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .grand-total-label {
            background-color: #cfece0 !important;
            color: #0c4b33 !important;
            font-size: 12.5px;
            font-weight: bold;
            border: 1px solid #b8dfd0 !important;
        }

        .grand-total-amount {
            background-color: #f7faf8 !important;
            color: #0c4b33 !important;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #b8dfd0 !important;
        }

        .declaration-signatory {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
            border: 1px solid #d0e4dc;
            border-collapse: collapse;
            background-color: #f7faf8;
        }

        .declaration-signatory td {
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }

        .declaration-cell {
            border-right: 1px solid #d0e4dc;
        }

        .signatory-cell {
            position: relative;
            height: 90px;
            text-align: right;
            vertical-align: top;
        }

        .signatory-company {
            font-size: 10px;
            color: #0c4b33;
            text-align: right;
        }

        .signatory-bottom {
            position: absolute;
            bottom: 8px;
            right: 8px;
            text-align: right;
            font-size: 10px;
            color: #0c4b33;
        }
    </style>
</head>

<body>
    @php
        $displayVal = function($val) {
            $trimmed = trim((string)($val ?? ''));
            return ($trimmed === '' || $trimmed === '-' || $trimmed === '--') ? 'N/A' : $val;
        };

        $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
        $displayClinicName = $clinic_name ?? \App\Models\Setting::where('key', 'clinic_name')->value('value') ?? 'Srinethi Clinic & Lakshmi Lab';

        // Logo resolution
        $resolvedLogoPath = null;
        $candidateLogos = array_filter([
            $clinic_logo ?? null,
            \App\Models\Setting::where('key', 'clinic_logo')->value('value'),
            'admin/assets/img/cliniclogo.png',
            'admin/assets/img/cliniclogohalf.png',
            'admin/assets/img/logo.png',
        ]);

        foreach ($candidateLogos as $candidate) {
            if (empty($candidate) || $candidate === 'default-logo.png') {
                continue;
            }
            if (file_exists($candidate) && is_file($candidate)) {
                $resolvedLogoPath = $candidate;
                break;
            }
            $cleanCandidate = ltrim(str_replace('\\', '/', $candidate), '/');
            if (file_exists(public_path($cleanCandidate)) && is_file(public_path($cleanCandidate))) {
                $resolvedLogoPath = public_path($cleanCandidate);
                break;
            }
            $baseCandidate = basename($candidate);
            if (file_exists(public_path('uploads/clinic_logos/' . $baseCandidate)) && is_file(public_path('uploads/clinic_logos/' . $baseCandidate))) {
                $resolvedLogoPath = public_path('uploads/clinic_logos/' . $baseCandidate);
                break;
            }
            if (file_exists(public_path('admin/assets/img/' . $baseCandidate)) && is_file(public_path('admin/assets/img/' . $baseCandidate))) {
                $resolvedLogoPath = public_path('admin/assets/img/' . $baseCandidate);
                break;
            }
        }

        $logoBase64 = null;
        if ($resolvedLogoPath && file_exists($resolvedLogoPath)) {
            $fileData = @file_get_contents($resolvedLogoPath);
            if ($fileData !== false) {
                $ext = pathinfo($resolvedLogoPath, PATHINFO_EXTENSION);
                $mime = ($ext === 'svg') ? 'image/svg+xml' : (($ext === 'webp') ? 'image/webp' : (($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png'));
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($fileData);
            }
        }

        $displayClinicPhone = $clinic_phone ?? \App\Models\Setting::where('key', 'clinic_phone')->value('value');
        $displayClinicEmail = $clinic_email ?? \App\Models\Setting::where('key', 'clinic_email')->value('value');
        $displayClinicAddress = $clinic_address ?? \App\Models\Setting::where('key', 'clinic_address')->value('value');
        $displayClinicCity = $clinic_city ?? \App\Models\Setting::where('key', 'clinic_city')->value('value');
        $displayClinicState = $clinic_state ?? \App\Models\Setting::where('key', 'clinic_state')->value('value');
        $addressParts = array_filter([$displayClinicAddress ?? '', $displayClinicCity ?? '', $displayClinicState ?? '']);

        // Pricing calculations
        $itemPrice = floatval($price ?? $treatment->price ?? 0);
        $taxRate = floatval($invoice->tax ?? 0);
        $taxAmount = ($taxRate > 0) ? (($itemPrice * $taxRate) / 100) : 0;
        $discountVal = floatval($invoice->discount ?? 0);
        $discountType = $invoice->discount_type ?? 'fixed_amount';
        $discountAmount = ($discountType === 'percentage')
            ? ((($itemPrice + $taxAmount) * $discountVal) / 100)
            : $discountVal;
        $grandTotal = ($invoice && !empty($invoice->grand_total))
            ? floatval($invoice->grand_total)
            : ($itemPrice + $taxAmount - $discountAmount);
    @endphp

    <div class="invoice-wrapper">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 40%; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Clinic Logo" style="max-height: 65px; max-width: 190px;">
                    @elseif(!empty($resolvedLogoPath) && file_exists($resolvedLogoPath))
                        <img src="{{ $resolvedLogoPath }}" alt="Clinic Logo" style="max-height: 65px; max-width: 190px;">
                    @else
                        <div class="clinic-title">{{ $displayClinicName }}</div>
                    @endif
                </td>
                <td style="width: 60%; text-align: right; vertical-align: middle;">
                    <div class="clinic-title">{{ $displayClinicName }}</div>
                    @if(!empty($addressParts))
                        <div class="clinic-sub">
                            {{ implode(', ', $addressParts) }}
                        </div>
                    @endif
                    <div class="clinic-sub">
                        @if(!empty($displayClinicPhone))PHONE: {{ $displayClinicPhone }}@endif
                        @if(!empty($displayClinicPhone) && !empty($displayClinicEmail)) | @endif
                        @if(!empty($displayClinicEmail))EMAIL: {{ $displayClinicEmail }}@endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Divider Line -->
        <hr class="header-divider">

        <!-- Patient and Appointment Details -->
        <table class="details-container">
            <tr>
                <!-- Patient Details -->
                <td class="details-box details-box-left">
                    <div class="section-heading">PATIENT DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Name</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($patient->fullname ? ucfirst($patient->fullname) : '') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Phone</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->phone ?? '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Address</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($patient->address ? ucfirst($patient->address) : '') }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Appointment Details -->
                <td class="details-box details-box-right">
                    <div class="section-heading">APPOINTMENT DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Appointment #</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($appointment->id ?? '') }}</strong></td>
                        </tr>
                        @if($invoice && !empty($invoice->type_id))
                        <tr>
                            <td class="info-label">Invoice #</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($invoice->type_id) }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="info-label">Doctor</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($doctor->fullname ? ucfirst($doctor->fullname) : '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Date & Time</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($appointment->date ?? '') }} {{ $appointment->duration ? '(' . $appointment->duration . ' mins)' : '' }}</td>
                        </tr>
                        @if($currentProjectTypeId !== 3)
                        <tr>
                            <td class="info-label">Status</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($appointment->status ? ucfirst($appointment->status) : '') }}</td>
                        </tr>
                        @endif
                        @if(!empty($appointment->payment_status))
                        <tr>
                            <td class="info-label">Payment Status</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ ucfirst($appointment->payment_status) }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Item Section Title Banner -->
        <div class="section-banner">
            Appointment Details
        </div>

        <!-- Appointment Items & Pricing Table -->
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 45%;">Treatment / Service</th>
                    <th style="width: 15%;">Qty</th>
                    <th style="width: 20%;">Price</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">
                        <strong>{{ $displayVal($treatment->name ?? 'Appointment Consultation') }}</strong>
                        @if(!empty($appointment->clinic_location) && $currentProjectTypeId !== 3)
                            <div style="font-size: 11px; color: #555;">Location: {{ $appointment->clinic_location }}</div>
                        @endif
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-center">Rs. {{ number_format($itemPrice, 2) }}</td>
                    <td class="text-center">Rs. {{ number_format($itemPrice, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">Rs. {{ number_format($itemPrice, 2) }}</td>
                </tr>
                @if($taxAmount > 0)
                <tr>
                    <td colspan="3" class="text-right"><strong>{{ ucfirst($tax_name ?? 'Tax') }} ({{ $taxRate }}{{ $tax_type === 'percentage' ? '%' : ' Rs' }}):</strong></td>
                    <td class="text-right">Rs. {{ number_format($taxAmount, 2) }}</td>
                </tr>
                @endif
                @if($discountAmount > 0)
                <tr>
                    <td colspan="3" class="text-right"><strong>Discount {{ $discountType === 'percentage' ? '(' . $discountVal . '%)' : '' }}:</strong></td>
                    <td class="text-right">Rs. {{ number_format($discountAmount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" class="text-right grand-total-label"><strong>Grand Total:</strong></td>
                    <td class="text-right grand-total-amount"><strong>Rs. {{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Banner for History -->
        @if(!empty($history) && $history->isNotEmpty())
        <div class="section-banner" style="margin-top: 8px;">
            Appointment Reschedule History
        </div>

        <!-- Appointment History Table -->
        <table class="invoice-box">
            <thead>
                <tr>
                    <th style="width: 25%;">Date</th>
                    <th style="width: 20%;">Time</th>
                    @if($currentProjectTypeId !== 3)
                    <th style="width: 20%;">Status</th>
                    @endif
                    <th style="width: {{ $currentProjectTypeId !== 3 ? '35%' : '55%' }};">Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $h)
                    <tr>
                        <td>{{ $displayVal($h->date ?? '') }}</td>
                        <td>{{ $displayVal($h->time ?? '') }}</td>
                        @if($currentProjectTypeId !== 3)
                        <td>{{ $displayVal($h->status ? ucfirst($h->status) : '') }}</td>
                        @endif
                        <td>{{ $displayVal($h->comment ?? '') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(($declaration_status ?? 'off') === 'on')
            <!-- Declaration and Authorized Signatory -->
            <table class="declaration-signatory">
                <tr>
                    <td class="declaration-cell">
                        @if(!empty($declaration))
                            <div style="font-size: 10px; font-weight: bold; color: #0c4b33; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">
                                Declaration:
                            </div>
                            <div style="font-size: 9.5px; color: #444; line-height: 1.35;">
                                {!! nl2br(e($declaration)) !!}
                            </div>
                        @endif
                    </td>
                    <td class="signatory-cell">
                        <div class="signatory-company">For, {{ $displayClinicName }}</div>
                        <div class="signatory-bottom">Authorized Signatory</div>
                    </td>
                </tr>
            </table>
        @endif

    </div>
</body>

</html>