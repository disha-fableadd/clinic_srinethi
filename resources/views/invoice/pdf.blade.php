<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            /* Reduced margin to fit within the PDF page */
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
            /* Reduced size */
            max-height: 40px;
            /* Reduced size */
        }

        .clinic-details {
            font-size: 12px;
            /* Reduced font size */
            padding-left: 8px;
            /* Reduced padding */
        }

        h2,
        h3 {
            margin-bottom: 3px;
            /* Reduced margin */
        }

        p {
            margin: 1px 0;
            /* Reduced margin */
        }

        .imgname {
            display: flex;
            align-items: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 90%;
            background-color: #cfece0;
            padding: 10px 20px;
            /* Reduced padding */
            font-size: 12px;
            /* Reduced font size */
            color: #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
        }

        .footer-left {
            line-height: 1.2;
            text-align: center;
            /* Reduced line height */

        }

        .footer-left strong {
            font-size: 14px;
            /* Reduced font size */
            color: #0c4b33;
        }

        .footer-logo img {
            max-height: 40px;
            /* Reduced size */
            max-width: 60px;
            /* Reduced size */
        }

        .left {
            padding-left: 40px;
            /* Reduced padding */
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
        }

        .footer-right {
            text-align: center;
        }


        .imgname {
            display: flex;
            align-items: center;
        }

        .clinic-logo {
            width: 50px;
            height: 50px;
        }

        .details {
            display: flex;
            flex-direction: column;
            padding-left: 10px;
        }

        .details strong {
            font-size: 18px;
        }

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        .invoice-box thead tr {
            background-color: #f2f2f2;
            text-align: left;
        }

        .invoice-box th,
        .invoice-box td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .invoice-box th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        /* .invoice-box tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        } */

        .invoice-box tbody tr:hover {
            background-color: #f1f1f1;
        }

        .invoice-box tfoot tr {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .invoice-box tfoot td {
            text-align: right;
        }

        .invoice-box .text-right {
            text-align: left;
        }

        .invoice-box .text-center {
            text-align: center;
        }

        .invoice-box strong {
            color: #333;
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
                        <strong style="font-size: 20px; text-transform: uppercase;">INVOICE</strong><br>
                        <strong>Invoice #:</strong> {{ $invoice_no }}<br>
                        <strong>Date:</strong> {{ $date }}
                    </div>
                </td>

            </tr>
        </table>
    </div>

    <!-- Patient and Clinic Info -->

    <!-- <div
        style="display: flex; justify-content: space-between; gap: 20px; border-bottom: 1px solid #537a6a; padding: 20px 0;">
        
        <div style="width: 48%; padding-right: 20px;">
            <h4 style="margin-bottom: 10px; font-weight: 600;">Patient Details</h4>
            <p><span style="font-weight: 600;">Name:</span> {{ $patient->fullname ?? '--' }}</p>
            <p><span style="font-weight: 600;">Phone:</span> {{ $patient->phone ?? '--' }}</p>
            <p><span style="font-weight: 600;">Address:</span> {{ $patient->address ?? '--' }}</p>
        </div>


    </div> -->
    <div
        style="display: flex; justify-content: space-between; gap: 20px; border-bottom: 1px solid #537a6a; padding: 20px 0;">
        <div style="width: 48%; padding-right: 20px;">
            <h4 style="margin-bottom: 10px; font-weight: 600;">Patient Details</h4>
            <p><span style="font-weight: 600;">Name:</span>
                {{ $patient->fullname ? ucfirst($patient->fullname) : '--' }}</p>
            <p><span style="font-weight: 600;">Phone:</span> {{ $patient->phone ? ucfirst($patient->phone) : '--' }}</p>
            <p><span style="font-weight: 600;">Address:</span>
                {{ $patient->address ? ucfirst($patient->address) : '--' }}</p>
        </div>
    </div>


    <!-- Item Section Title -->
    <h3>
        @if($invoice_type === 'medicine')
            Medicines:
        @elseif($invoice_type === 'treatment')
            Treatments:
        @elseif($invoice_type === 'service')
            Services:
        @elseif($invoice_type === 'appointment')
            Appointment:
        @elseif($invoice_type === 'therapy')
            Therapy:
        @elseif($invoice_type === 'discharge')
            Discharge:
        @else
            Items:
        @endif
    </h3>

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
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                @if($has_gst)
                    <th>GST Option</th>
                    <th>GST (%)</th>
                    <th>GST Amount</th>
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
                        <td>{{ $service['name'] ?? '--' }}</td>
                        <td>{{ $service['quantity'] ?? 0 }}</td>
                        <td>{{ number_format($service['price'] ?? 0, 2) }}</td>
                        <td>{{ number_format($service['total'] ?? 0, 2) }}</td>
                        @if($has_gst)
                            <td>{{ $gstOpt }}</td>
                            <td>{{ $gstOpt === 'With GST' ? number_format($gstRates, 2) : '--' }}</td>
                            <td>{{ $gstOpt === 'With GST' ? number_format($gstAmt, 2) : '--' }}</td>
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
                <td>Rs. {{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($total_gst_amount > 0)
            <tr>
                <td colspan="{{ $colspan_foot }}" class="text-right"><strong>GST Amount:</strong></td>
                <td>Rs. {{ number_format($total_gst_amount, 2) }}</td>
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
                    <td>Rs. {{ number_format($tax_amount, 2) }}</td>
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
                    <td>Rs. {{ number_format($discount_amount, 2) }}</td>
                </tr>
            @endif



            <tr>
                <td colspan="{{ $colspan_foot }}" class="text-right"><strong>Grand Total (incl. tax):</strong></td>
                <td><strong>Rs. {{ number_format($total_with_tax ?? 0, 2) }}</strong></td>
            </tr>

            <tr>
                <td colspan="{{ $colspan_foot }}" class="text-right"><strong>Payment Mode:</strong></td>
                <td>{{ ucfirst($payment_type ?? 'N/A') }}</td>
            </tr>

            <tr>
                <td colspan="{{ $colspan_foot }}" class="text-right"><strong>Payment Status:</strong></td>
                <td>{{ ucfirst($payment_status ?? 'pending') }}</td>
            </tr>

        </tfoot>

    </table>

    <!-- Footer -->
    <div class="container footer">


        <div class="footer-right" width="50%">
            For more information or any issues or concerns,<br>
            email us at <strong>{{ $clinic_email ?? 'clinic@example.com' }} </strong>or contact
            <strong>{{ $clinic_phone ?? '9876543210' }}</strong>
        </div>
    </div>

</body>

</html>