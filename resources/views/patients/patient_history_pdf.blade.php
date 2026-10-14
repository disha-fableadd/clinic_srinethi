<!DOCTYPE html>
<html>

<head>
    <title>Patient Full History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .page-border {
            border: 1px solid #a0a0a0;
            padding: 15px;
            box-sizing: border-box;
            height: 1010px;
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

        .invoice-box {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .invoice-box th,
        .invoice-box td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .invoice-box thead tr {
            background-color: #f2f2f2;
        }


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

        .footer-right {
            text-align: center;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* equal columns */
            margin: 0 0 6px 0;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
            padding-right: 24px;
            /* gap between columns */
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
                                {{ $clinic_phone ?? '--' }} || {{ $clinic_email ?? 'clinic@example.com' }}
                            </div>
                        </div>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: middle;">
                        <div style="line-height: 1.6; font-size: 13px;">
                            <strong style="font-size: 20px; text-transform: uppercase;">Patient Details</strong><br>
                            <strong>PATIENT ID:</strong> {{ $patient->patient_unique_id ?? 'N/A' }}<br>
                            <strong>Date:</strong> {{ $date }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Patient Info -->
        <div class="section">
            <h3>Patient Details</h3>

            <table class="two-col">
                <tr>
                    <td>
                        <p><strong>Name:</strong> {{ $patient->fullname ?? '--' }}</p>
                        <p><strong>Phone:</strong> {{ $patient->phone ?? '--' }}</p>
                        @php
                            $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                        @endphp
                        @if($currentProjectTypeId !== 3)
                        <p><strong>Email:</strong> {{ $patient->email ?? '--' }}</p>
                        @endif
                        <p><strong>Address:</strong> {{ $patient->address ?? '--' }}</p>
                        <p><strong>City:</strong> {{ $patient->city ?? '--' }}</p>
                         <p><strong>Symptom Remarks:</strong> {{ $patient->symptom_remarks ?? '--' }}</p>
                    </td>
                    <td>
                        <p><strong>State:</strong> {{ $patient->state ?? '--' }}</p>
                        <p><strong>Referral Source:</strong> {{ $patient->referral_source ?? '--' }}</p>
                        <p><strong>Birthdate:</strong> {{ $patient->birthdate ?? '--' }}</p>
                        <p><strong>Age:</strong> {{ $patient->age ?? '--' }}</p>
                       


                    </td>
                </tr>
            </table>
        </div>


        <!-- Followup History -->
        <div class="section">
            <h3>Followup History</h3>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        @if($currentProjectTypeId !== 3)
                        <th>Treatment</th>
                        @endif
                        <th>Date</th>
                        @if($currentProjectTypeId !== 3)
                        <th>Type</th>
                        @endif

                    </tr>
                </thead>
                <tbody>
                    @forelse($followups as $f)
                        <tr>
                            <td>{{ $f->doctor->fullname ?? '--' }}</td>
                            @if($currentProjectTypeId !== 3)
                            <td>{{ $f->treatment->name ?? '--' }}</td>
                            @endif
                            <td>{{ $f->date ?? '--' }}</td>
                            @if($currentProjectTypeId !== 3)
                            <td>{{ $f->followup_type ?? '--' }}</td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No followup history available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Appointments History -->
        <div class="section">
            <h3>Appointments History</h3>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Treatment</th>
                        <th>Date</th>
                        @if($currentProjectTypeId !== 3)
                        <th>Appointment Type</th>
                        <th>Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $a)
                        <tr>
                            <td>{{ $a->doctor->fullname ?? '--' }}</td>
                            <td>{{ $a->treatment->name ?? '--' }}</td>
                            <td>{{ $a->date ?? '--' }}</td>
                            @if($currentProjectTypeId !== 3)
                            <td>{{ $a->appoint_type ?? '--' }}</td>
                            <td>{{ ucfirst($a->status) ?? '--' }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No appointments history available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Payment History -->
        <div class="section">
            <h3>Payment History</h3>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Paid Amount</th>
                        <th>Remaining</th>
                        <th>Collected By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            {{-- Date from daily_data --}}
                            <td>{{ $p->dailyData->date ? \Carbon\Carbon::parse($p->dailyData->date)->format('d-m-Y') : '--' }}
                            </td>

                            {{-- Amounts from payment_history --}}
                            <td>{{ $p->amount ?? '--' }}</td>
                            <td>{{ $p->paid_amount ?? '--' }}</td>
                            <td>{{ $p->remain_amount ?? '--' }}</td>

                            {{-- Collected by user (via daily_data.collect_by_id relation) --}}
                            <td>{{ $p->dailyData->collectedBy->fullname ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No payment history available.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Report History -->
        <div class="section">
            <h3>Report History</h3>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Report Type</th>
                        <th>Description</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                        <tr>
                            <td>{{ $r->date ?? '--' }}</td>
                            <td>{{ $r->report_type ?? '--' }}</td>
                            <td>{{ $r->description ?? '--' }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No report history available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Treatment History -->
        <div class="section">
            <h3>Treatment History</h3>
            <table class="invoice-box">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Treatment</th>
                        <th>Plan</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($treatmentPayments as $tp)
                        <tr>
                            <td>{{ $tp->booking->treatment->doctor->fullname ?? '--' }}</td>

                            <td>{{ $tp->booking->treatment->name ?? '--' }}</td>
                            <td>{{ $tp->booking->plan ?? '--' }}</td>
                            <td>{{ $tp->booking->payment_date ? \Carbon\Carbon::parse($tp->booking->payment_date)->format('d-m-Y') : '--' }}
                            </td>
                            <td>{{ $tp->booking->status ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No treatment history available.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>
        
        
                </tbody>
            </table>

        </div> 

        
        
        
                </tbody>
            </table>

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
