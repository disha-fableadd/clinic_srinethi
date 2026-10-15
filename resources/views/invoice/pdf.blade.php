<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
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
            width: 52%;
            border-right: 1px solid #d0e4dc;
        }

        .details-box-right {
            width: 48%;
        }

        .section-heading {
            font-size: 12px;
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
            font-size: 12px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-label {
            width: 95px;
            color: #555;
            font-weight: 500;
        }

        .info-sep {
            width: 12px;
            color: #555;
            text-align: center;
        }

        .info-val {
            color: #222;
        }

        .section-banner {
            color: #222;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            padding: 5px 0;
            letter-spacing: 1px;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 12px;
        }

        .invoice-box th {
            background-color: #cfece0;
            color: #0c4b33;
            font-weight: bold;
            text-align: left;
            padding: 7px 10px;
            border: 1px solid #b8dfd0;
            font-size: 11.5px;
            letter-spacing: 0.3px;
        }

        .invoice-box td {
            padding: 7px 10px;
            border: 1px solid #d5e5dd;
            color: #333;
            font-size: 12px;
        }

        .invoice-box tbody tr:nth-child(even) {
            background-color: #fbfdfc;
        }

        .invoice-box tfoot td {
            padding: 6px 10px;
            border: 1px solid #d5e5dd;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
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
            font-size: 13.5px;
            font-weight: bold;
            border: 1px solid #b8dfd0 !important;
        }

        .footer-note {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #c0d8cd;
            text-align: center;
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }

        .declaration-signatory {
            width: 100%;
            margin-top: 14px;
            page-break-inside: avoid;
            border: 1px solid #d0e4dc;
            border-collapse: collapse;
            background-color: #f7faf8;
        }

        .declaration-signatory td {
            width: 50%;
            padding: 7px 10px;
            vertical-align: top;
        }

        .declaration-cell {
            border-right: 1px solid #d0e4dc;
        }

        .signatory-cell {
            height: 64px;
            text-align: right;
            vertical-align: bottom !important;
        }
    </style>
</head>

<body>
    @php
        $displayVal = function($val) {
            $trimmed = trim((string)($val ?? ''));
            return ($trimmed === '' || $trimmed === '-' || $trimmed === '--') ? 'N/A' : $val;
        };
    @endphp

    <div class="invoice-wrapper">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 45%; vertical-align: middle;">
                    @if(!empty($clinic_logo))
                        <img src="{{ $clinic_logo }}" alt="Clinic Logo" style="max-height: 60px; max-width: 190px;">
                    @else
                        <div class="clinic-title">{{ $clinic_name ?? 'Clinic Name' }}</div>
                    @endif
                </td>
                <td style="width: 55%; text-align: right; vertical-align: middle;">
                    <div class="clinic-title">{{ $clinic_name ?? 'Clinic Name' }}</div>
                    @php
                        $addressParts = array_filter([$clinic_address ?? '', $clinic_city ?? '', $clinic_state ?? '']);
                    @endphp
                    @if(!empty($addressParts))
                        <div class="clinic-sub">
                            {{ implode(', ', $addressParts) }}
                        </div>
                    @endif
                    <div class="clinic-sub">
                        PHONE: {{ $displayVal($clinic_phone ?? '') }} | EMAIL: {{ $displayVal($clinic_email ?? '') }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Divider Line -->
        <hr class="header-divider">

        <!-- Patient and Invoice Details -->
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

                <!-- Invoice Details -->
                <td class="details-box details-box-right">
                    <div class="section-heading">INVOICE DETAILS:</div>
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Invoice #</td>
                            <td class="info-sep">:</td>
                            <td class="info-val"><strong>{{ $displayVal($invoice_no ?? '') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="info-label">Date</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal($date ?? '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Payment Mode</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal(!empty($payment_type) ? ucfirst($payment_type) : '') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Payment Status</td>
                            <td class="info-sep">:</td>
                            <td class="info-val">{{ $displayVal(!empty($payment_status) ? ucfirst($payment_status) : '') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Item Section Title Banner -->
        <div class="section-banner">
            @if($invoice_type === 'medicine')
                Medicines
            @elseif($invoice_type === 'treatment')
                Treatments
            @elseif($invoice_type === 'service')
                Services
            @elseif($invoice_type === 'appointment')
                Appointment
            @elseif($invoice_type === 'therapy')
                Therapy
            @elseif($invoice_type === 'discharge')
                Discharge
            @else
                Items
            @endif
        </div>

        @php
            $has_gst = !empty($services) && collect($services)->contains(fn($s) => ($s['gst_option'] ?? '') === 'With GST' && !empty($s['product_gst']));
        @endphp

        <!-- Invoice Table -->
        <table class="invoice-box">
            <thead>
                <tr>
                    <th>
                        @if($invoice_type === 'medicine')
                            Medicine Name
                        @elseif($invoice_type === 'treatment')
                            Treatment Name
                        @elseif($invoice_type === 'service')
                            Service Name
                        @elseif($invoice_type === 'appointment')
                            Appointment
                        @elseif($invoice_type === 'therapy')
                            Therapy
                        @elseif($invoice_type === 'discharge')
                            Discharge
                        @else
                            Item Name
                        @endif
                    </th>
                    <th class="text-center" style="width: 12%;">Quantity</th>
                    <th class="text-right" style="width: 18%;">Unit Price</th>
                    <th class="text-right" style="width: 18%;">Total</th>
                    @if($has_gst)
                        <th class="text-center" style="width: 14%;">GST Option</th>
                        <th class="text-center" style="width: 12%;">GST (%)</th>
                        <th class="text-right" style="width: 14%;">GST Amount</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if(!empty($services) && count($services) > 0)
                    @foreach($services as $service)
                        @php
                            $gstOpt = $service['gst_option'] ?? 'Without GST';
                            $productGst = $service['product_gst'] ?? null;
                            $gstRates = is_array($productGst) ? array_sum(array_map(fn($t) => floatval($t['tax_rate'] ?? 0), $productGst)) : 0;
                            $gstAmt = is_array($productGst) ? array_sum(array_map(fn($t) => floatval($t['tax_amount'] ?? 0), $productGst)) : 0;
                        @endphp
                        <tr>
                            <td>{{ $displayVal($service['name'] ?? '') }}</td>
                            <td class="text-center">{{ $service['quantity'] ?? 0 }}</td>
                            <td class="text-right">{{ number_format($service['price'] ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($service['total'] ?? 0, 2) }}</td>
                            @if($has_gst)
                                <td class="text-center">{{ $displayVal($gstOpt) }}</td>
                                <td class="text-center">{{ $gstOpt === 'With GST' ? number_format($gstRates, 2) : 'N/A' }}</td>
                                <td class="text-right">{{ $gstOpt === 'With GST' ? number_format($gstAmt, 2) : 'N/A' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ $has_gst ? 7 : 4 }}" class="text-center">No items found.</td>
                    </tr>
                @endif
            </tbody>

            <tfoot>
                @php
                    $discount = $discount ?? 0;
                    $tax = $tax ?? 0;
                    $discount_type = $discount_type ?? 'fixed_amount';
                    $tax_type = $tax_type ?? '%';

                    $subtotal = collect($services)->sum(function ($item) {
                        return floatval($item['total'] ?? 0);
                    });

                    $total_gst_amount = 0;
                    if (!empty($services)) {
                        foreach ($services as $item) {
                            if (($item['gst_option'] ?? '') === 'With GST' && !empty($item['product_gst']) && is_array($item['product_gst'])) {
                                $total_gst_amount += array_sum(array_map(fn($t) => floatval($t['tax_amount'] ?? 0), $item['product_gst']));
                            }
                        }
                    }

                    $base_for_tax = $subtotal + $total_gst_amount;
                    $tax_amount = ($base_for_tax * $tax) / 100;
                    $discount_amount = $discount_type === 'percentage'
                        ? (($base_for_tax + $tax_amount) * $discount) / 100
                        : $discount;

                    $total_with_tax = $base_for_tax + $tax_amount - $discount_amount;
                    $colspan_foot = $has_gst ? 6 : 3;
                @endphp

                <tr>
                    <td colspan="{{ $colspan_foot }}" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">Rs. {{ number_format($subtotal, 2) }}</td>
                </tr>

                @if($total_gst_amount > 0)
                <tr>
                    <td colspan="{{ $colspan_foot }}" class="text-right"><strong>GST Amount:</strong></td>
                    <td class="text-right">Rs. {{ number_format($total_gst_amount, 2) }}</td>
                </tr>
                @endif

                @if(($tax_amount ?? 0) > 0)
                    <tr>
                        <td colspan="{{ $colspan_foot }}" class="text-right">
                            <strong>
                                {{ ucfirst($tax_name ?? 'Tax') }}
                                ({{ $tax ?? 0 }}{{ $tax_type == 'percentage' ? '%' : ' Rs' }}):
                            </strong>
                        </td>
                        <td class="text-right">Rs. {{ number_format($tax_amount, 2) }}</td>
                    </tr>
                @endif

                @if(($discount_amount ?? 0) > 0)
                    <tr>
                        <td colspan="{{ $colspan_foot }}" class="text-right">
                            <strong>
                                Discount
                                @if(($discount_type ?? '') === 'percentage')
                                    ({{ $discount ?? 0 }}%)
                                @endif:
                            </strong>
                        </td>
                        <td class="text-right">Rs. {{ number_format($discount_amount, 2) }}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="{{ $colspan_foot }}" class="text-right grand-total-label">
                        <strong>Grand Total (incl. tax):</strong>
                    </td>
                    <td class="text-right grand-total-amount">
                        <strong>Rs. {{ number_format($total_with_tax ?? 0, 2) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        @if(($declaration_status ?? 'off') === 'on')
            <!-- Declaration and Authorized Signatory -->
            <table class="declaration-signatory">
                <tr>
                    <td class="declaration-cell">
                        @if(!empty($declaration))
                            <div style="font-size: 10.5px; font-weight: bold; color: #0c4b33; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">
                                Declaration:
                            </div>
                            <div style="font-size: 10px; color: #444; line-height: 1.35;">
                                {!! nl2br(e($declaration)) !!}
                            </div>
                        @endif
                    </td>
                    <td class="signatory-cell">
                        <div style="font-size: 10px; color: #0c4b33; margin-bottom: 18px;">For, Fablead Developer</div>
                        <div style="font-size: 10px; color: #0c4b33;">Authorized Signatory</div>
                    </td>
                </tr>
            </table>
        @endif

        <!-- Footer Note -->
        <div class="footer-note">
            For more information or any issues or concerns, email us at
            <strong>{{ $clinic_email ?? 'clinic@example.com' }}</strong> or contact
            <strong>{{ $clinic_phone ?? '9876543210' }}</strong>
        </div>

    </div>

</body>

</html>
