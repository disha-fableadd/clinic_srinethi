@extends('layout.app')

<style>
    /* popup box styling */
    .custom-swal-popup {
        border-radius: 12px;
        padding: 20px;
    }

    /* title styling */
    .custom-swal-title {
        font-size: 22px;
        font-weight: 600;
        color: #333;
    }

    /* description text */
    .custom-swal-text {
        font-size: 16px;
        color: #666;
    }

    /* Yes button */
    .swal-confirm-btn {
        background-color: #cfece0 !important;
        color: black !important;
        /* font-weight: 600; */
        border-radius: 6px;
        padding: 8px 18px;
        border: none;
        margin: 5px;
        cursor: pointer;
    }

    button.btn.btn-sm.btn-light.expand-btn {
        background: #f89884;
        color: white;
        border: none;
        border-radius: 9px;
        padding: 7px 9px;
    }

    .action-icons {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
    }

    .action-icons i,
    .action-icons a {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 30px;
        height: 30px;
        background: #f89884;
        color: white;
        border-radius: 8px;
        font-size: 14px;
        text-decoration: none;
    }
    .icon {
    font-size: 11px !important;
    /* display: flex; */
}

    span.amount {
        margin-right: 34px;
    }

    button.btn.btn-sm.btn-light.expand-btn {
        background: #f89884;
        color: white;
        border: none;
        border-radius: 9px;
        padding: 7px 9px;
    }

    .action-icons {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
    }

    .action-icons i,
    .action-icons a {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 30px;
        height: 30px;
        background: #f89884;
        color: white;
        border-radius: 8px;
        font-size: 14px;
        text-decoration: none;
    }

    .action-icons i:hover,
    .action-icons a:hover {
        background: #f67560;
    }

    /* amount bold */
    .summary .amount {
        font-weight: bold;
    }

    /* Cancel button */
    .swal-cancel-btn {
        background-color: #f89884 !important;
        color: white !important;
        /* font-weight: 600; */
        border-radius: 6px;
        padding: 8px 18px;
        border: none;
        margin: 5px;
        cursor: pointer;
    }

    /* hover effect */
    .swal-confirm-btn:hover {
        background-color: #b7e2d3 !important;
    }

    .swal-cancel-btn:hover {
        background-color: #f77963 !important;
    }


    .filter {
        border-color: rgb(207 236 224) !important;
    }

    .table td,
    .table th {
        vertical-align: middle;
        text-align: center;
        font-size: 14px;
    }

    .group-row {
        background: #f8f9fa;
        font-weight: 500;
    }

    .group-header-row th {
        background-color: #f0f0f0;
    }

    .toggle-group {
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .group-table {
        table-layout: fixed;
        width: 100%;
        max-width: 100%;
    }

    .btn-outline-primary {
        border-color: #cfece0;
        color: black !important;
        background-color: #cfece0 !important;
    }

    .table.custom-table>tbody>tr>td,
    .table.custom-table>tbody>tr>th,
    .table.custom-table>tfoot>tr>td,
    .table.custom-table>tfoot>tr>th,
    .table.custom-table>thead>tr>td,
    .table.custom-table>thead>tr>th {
        padding: 10px 0 !important;
        vertical-align: middle;
    }

    .dt-column-title {
        padding: 0 20px !important;
    }

    colgroup {
        display: none;
    }

    button.btn.btn-link.expand-btn {
        background: #f89884;
        color: white;
        padding: 7px;
        border-radius: 9px;
    }

    .details-row {
        background-color: #f8f9fa;
    }

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
        text-align: left !important;
        line-height: 20px !important;
    }

    .details-content strong {
        font-weight: 700 !important;
        color: #000 !important;
    }

    .action-row {
        display: flex;
        align-items: center;
        gap: 10px;
        /* spacing between label and icons */
    }

    #daily_datatbl thead th {
        vertical-align: middle !important;
        padding: 8px !important;
    }

    .expand-btn {
        border: none;
        background: none;
        color: #f89884;
        font-size: 16px;
    }

    .expand-btn:hover {
        color: #f89884;
    }

    @media (min-width: 768px) {
        .d-table-cell.d-md-none {
            display: none !important;
        }

        .details-row {
            display: none !important;
        }
    }

    @media (max-width: 767px) {
        .d-none.d-md-table-cell {
            display: none !important;
        }
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;
        }
    }

    div:where(.swal2-container) .swal2-input {

        height: auto !important;
        padding: 8px !important;
        width: 100% !important;
        margin: 0 !important;
        border: 1px solid #f89884 !important;
        border-radius: 50px !important;
    }

    select:not(:-internal-list-box):not([multiple]) {
        border: 1px solid #f89884 !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-confirm) {
        background-color: #f89884 !important;
        color: black !important;
        border-radius: 50px !important;
        font-size: 15px !important;
        font-weight: 500 !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-cancel) {
        border-radius: 50px !important;
        font-size: 15px !important;
        font-weight: 500 !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled) {
        padding: 8px 12px !important;
    }


    tr.group-row {
        cursor: pointer;
    }

    /* Base table styling */
    .table.custom-table {
        border-collapse: collapse;
        width: 100%;
        table-layout: auto;
    }

    @media (max-width: 768px) {
        .icon {
            /* flex-direction: column; */
            align-items: center;
            gap: 8px;
            /* spacing between buttons */
        }

        .mobile_colums {
            flex-direction: column;
            align-items: self-start !important;
            gap: 4px !important;
        }

        .mobile_colums .action-icons .icon {
            flex-direction: row;
            gap: 3px;
        }

        .icon i {
            width: 30px;
            height: 30px;
            font-size: 12px;

        }

        button.btn.btn-sm.btn-outline-primary.make-payment-btn {
            font-size: 0px;
            margin: 0 7px;
            margin-top: 2px;
        }

        thead {
            font-size: 10px;
        }

    }

    /* On small screens: enable horizontal scroll */
    @media (max-width: 768px) {
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;

        }

        .table.custom-table {
            /* min-width: 700px; */
            /* adjust based on number of columns */
            white-space: nowrap;
            /* keep data in one line */
        }


        .table.custom-table th,
        .table.custom-table td {
            /* font-size: 11px; */
            padding: 6px 10px;
        }



    }

    /* Mobile-specific styles */
    @media (max-width: 767px) {

        /* Summary row layout */
        .group-row .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 5px;
        }

        /* Date, total, received, pending font size */
        .group-row .d-flex>div,
        .group-row .summary span.amount {
            font-size: 16px !important;
            font-weight: 600;
        }

        /* Stack amount, received, pending, and buttons vertically */
        .group-row .summary {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 5px;
            margin-top: 10px;
            width: 100%;
        }

        /* Buttons container */
        .group-row .daily-action-btns {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            width: 100%;
            justify-content: flex-start;
        }

        /* Make Export & Add buttons the same width */
        .group-row .daily-action-btns .btn {
            width: 45%;
            min-width: 0;
            text-align: center;
            padding: 6px 0;
        }



        /* Details row dropdown button */
        .toggle-group {
            font-size: 18px;
            margin-right: 10px;
        }

        /* Details table for mobile */
        .group-table {
            font-size: 14.5px;
        }

        /* Hide unnecessary columns in details table for mobile */
        .group-table th:nth-child(3),
        .group-table td:nth-child(3),
        .group-table th:nth-child(6),
        .group-table td:nth-child(6) {
            display: none;
        }
    }

    .btn-outline-success:hover {
        color: black !important;
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
    }

    .btn-outline-success {
        color: black !important;

        border-color: #cfece0 !important;
    }

    .icon {
        font-size: 11px !important;
        /* display: flex; */

    }
</style>
@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
@endphp

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">


            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="background-color:#f89884;">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-flask px-2" style="font-size:20px"></i>All Daily Payment
                            </h3>

                            @if (app('hasPermission')(30, 'create'))
                                <a href="{{ route('daily_data.create') }}" class="btn btn-rounded btn-hdr"
                                    style="background-color: #fed9cf;">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">


                            <div class="row mb-3 mx-auto">
                                <div class="col-md-3 col-sm-6 col-6 mt-2">
                                    <label>Year</label>
                                    <select id="filterYear" class="form-control yearfilter select2 filter">
                                        <option value="">All</option>
                                        {{-- Options will be appended by JS --}}
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-6 col-6 mt-2">
                                    <label>Month</label>
                                    <select id="filterMonth" class="selectmonth select2 form-control filter">
                                        <option value="" selected>All</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}">
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>


                                <div class="col-md-3 col-sm-6 col-12 mt-2">
                                    <label>Date</label>
                                    <input type="date" id="filterDate" class="form-control filter" />

                                </div>

                                <!-- Patient Filter -->
                                <div class="col-md-3 col-sm-6 col-12 mt-2">
                                    <label>Patient</label>
                                    <select id="filterPatient" class="form-control filter" style="width:100%;">
                                        <option value="">All</option>
                                    </select>
                                </div>

                            </div>
                            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                <div id="monthly-summary"></div>
                            @endif
                            <div class="table-responsive">
                                <table id="daily_datatbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Date</th>

                                        </tr>
                                    </thead>

                                    <tbody id="daily_dataTableBody">
                                        <!-- Dynamic content -->
                                    </tbody>
                                </table>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS & DataTable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
        // 🔹 Remove all colspan when resized to desktop view
        $(window).on("resize", function () {
            const table = $("table"); // or use a more specific selector if needed
            const isDesktop = window.innerWidth >= 768; // match your Bootstrap breakpoint

            if (isDesktop) {
                // Remove all colspan + reset headers
                table.find("td:first, td:last").removeAttr("colspan");
                table.find("th:first, th:last").removeAttr("colspan");
                $("table th:first, table th:last, table td:first, table td:last").removeAttr("colspan");
            }
        });

        // Click ANYWHERE on the group row to open/close its details
        $(document).on('click', 'tr.group-row', function (e) {
            // Don’t toggle if the click is on interactive controls or inside the child table
            if ($(e.target).closest(
                '.child-table-container, button, a, .exportButton, #addBtn, .make-payment-btn, .edit-daily_data, .delete-daily_data, .payment-history'
            ).length) {
                return;
            }

            // Get the target class from the caret icon in this row
            const targetClass = $(this).find('.toggle-group').data('target'); // e.g., "group-20250822"

            // IMPORTANT: details container uses a CLASS, so select with a dot:
            const $targetContainer = $(this).find(`.${targetClass}`);

            // Toggle only THIS container
            $targetContainer.toggleClass('d-none');

            // Flip the caret only for this row
            $(this).find('.toggle-group').toggleClass('fa-caret-right fa-caret-down');
        });

        $('.yearfilter').select2({

            width: '100%',
            placeholder: "Select Year",
            allowClear: true,
            minimumResultsForSearch: Infinity
        });
        $('.yearfilter').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search Year');
        });


        $('.selectmonth').select2({
            placeholder: "Select Month",
            allowClear: true,
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
        $('.selectmonth').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search month');
        });







        $(document).on('click', '.exportButton', function () {
            let date = $(this).closest('.group-row').find('.toggle-group').data('target').replace('group-', '');
            let formattedDate = date.replace(/^(\d{4})(\d{2})(\d{2})$/, '$1-$2-$3');
            window.location.href = `/export-daily-data/${formattedDate}`;
        });

        $(document).on('click', '.make-payment-btn', function () {
            const dailyId = $(this).data('id');


            $.ajax({
                url: `api/daily-data/${dailyId}/payment-history`,
                method: 'GET',

                success: function (response) {
                    const history = response.history || [];
                    const remainAmount = response.remaining || 0;
                    const payableAmount = response.payable || 0;

                    // Build Previous Payments HTML
                    let historyHtml = '';
                    if (history.length > 0) {
                        history.forEach(function (payment) {
                            let paymentDate = new Date(payment.created_at);
                            let formattedDate = paymentDate.toLocaleString('en-IN', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            historyHtml +=
                                `
                                    <div style="border:1px solid #f89884; padding:8px; border-radius:5px; margin-bottom:5px;">
                                        <div style="display:flex; justify-content:space-between; align-items:center;">
                                            <div style="font-weight:500; font-size:14px; color:#666;">${formattedDate}</div>
                                            <div style="font-weight:700; font-size:14px; text-align:right;">₹${payment.paid_amount} via ${payment.payment_mode}</div>
                                        </div>
                                    </div>
                                `;
                        });
                    } else {
                        historyHtml = `<div style="color:#999;">No previous payments.</div>`;
                    }

                    Swal.fire({
                        title: `<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                                                        <span style="font-size:18px; font-weight:bold;">Make Payment</span>
                                                                        <span style="font-size:14px; color:#666;">Payable: ₹${payableAmount}</span>
                                                                    </div>`,


                        html: `

                                                                    <hr>
                                                                    <div style="text-align:left; max-height:400px; overflow-y:auto;">
                                                                        <h6 style="margin:0; margin-bottom:5px; font-weight:bold; font-size:14px;">Previous Payments</h6>
                                                                        ${historyHtml}

                                                                        <label style="margin-top:10px;font-size:14px; font-weight:bold;">Remaining Balance</label>
                                                                        <input type="text" class="swal2-input" style="font-size:14px;" value="${remainAmount}" readonly>



                                                                        <label style="margin-top:10px;font-size:14px; font-weight:bold;">Payment Method</label>
                                                                        <select id="payment_mode" style="font-size:14px;" class="swal2-input">
                                                                            <option value="">Select</option>
                                                                            <option value="Cash">Cash</option>
                                                                            <option value="Online">Online</option>
                                                                                <option value="Cash+Online">Cash+Online</option>
                                                                        </select>


                                                                            <div id="cash_amount_group" style="display:none;">
                                                                                <label style="margin-top:10px; font-size:14px; font-weight:bold;">Cash Amount</label>
                                                                                <input type="number" id="cash_amount" style="font-size:14px;" class="swal2-input" placeholder="Enter cash amount">
                                                                            </div>

                                                                            <!-- Online Amount Field -->
                                                                            <div id="online_amount_group" style="display:none;">
                                                                                <label style="margin-top:10px; font-size:14px; font-weight:bold;">Online Amount</label>
                                                                                <input type="number" id="online_amount" style="font-size:14px;" class="swal2-input" placeholder="Enter online amount">

                                                                            </div>
                                                                            <label style="margin-top:10px; font-size:14px; font-weight:bold;">Payment Amount</label>
                                                                        <input type="number" id="paid_amount"  style="font-size:14px;" class="swal2-input" placeholder="Enter amount">
                                                                    </div>

                                                                    </div>

                                                                `,
                        showCancelButton: true,
                        confirmButtonText: 'Submit Payment',
                        cancelButtonText: 'Close',
                        focusConfirm: false,
                        didOpen: () => {
                            // Show/Hide fields dynamically
                            $(document).on('change', '#payment_mode', function () {
                                let mode = $(this).val();

                                if (mode === "Cash") {
                                    $('#cash_amount_group').show();
                                    $('#online_amount_group').hide();
                                    $('#paid_amount').prop('readonly', false).val(
                                        '');
                                } else if (mode === "Online") {
                                    $('#cash_amount_group').hide();
                                    $('#online_amount_group').show();
                                    $('#paid_amount').prop('readonly', false).val(
                                        '');
                                } else if (mode === "Cash+Online") {
                                    $('#cash_amount_group').show();
                                    $('#online_amount_group').show();
                                    $('#paid_amount').prop('readonly', true).val(
                                        '');
                                } else {
                                    $('#cash_amount_group').hide();
                                    $('#online_amount_group').hide();
                                    $('#paid_amount').prop('readonly', false).val(
                                        '');
                                }
                            });


                            // Auto-fill total (for Cash+Online)
                            $(document).on('input', '#cash_amount, #online_amount',
                                function () {
                                    let cash = parseFloat($('#cash_amount').val()) || 0;
                                    let online = parseFloat($('#online_amount')
                                        .val()) || 0;
                                    $('#paid_amount').val(cash + online);
                                });
                        },
                        preConfirm: () => {
                            const amount = parseFloat(document.getElementById('paid_amount')
                                .value);
                            const mode = document.getElementById('payment_mode').value;
                            const remaining = parseFloat(remainAmount);

                            let cashAmount = null;
                            let onlineAmount = null;

                            if (mode === "Cash+Online") {
                                cashAmount = parseFloat($('#cash_amount').val()) || 0;
                                onlineAmount = parseFloat($('#online_amount').val()) || 0;
                            }

                            if (!amount || amount <= 0) {
                                Swal.showValidationMessage('Please enter a valid amount.');
                                return false;
                            }

                            if (amount > remaining) {
                                Swal.showValidationMessage(
                                    `Payment amount cannot exceed ₹${remaining}.`);
                                return false;
                            }

                            if (!mode) {
                                Swal.showValidationMessage(
                                    'Please select a payment method.');
                                return false;
                            }

                            return {
                                paid_amount: amount,
                                payment_mode: mode,
                                cash_amount: cashAmount,
                                online_amount: onlineAmount
                            };
                        }


                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `api/daily-data/${dailyId}/make-payment`,
                                method: 'POST',
                                data: {
                                    paid_amount: result.value.paid_amount,
                                    payment_mode: result.value.payment_mode,
                                    cash_amount: result.value.cash_amount,
                                    online_amount: result.value.online_amount,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (res) {
                                    // Swal.fire('Success', res.message, 'success');

                                    // location.reload();
                                    // loadDailyData();
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Payment Saved     Successfully!',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location
                                            .reload(); // Reload the page to update the list
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire('Error', xhr.responseJSON.message ||
                                        'Payment failed.', 'error');
                                }
                            });
                        }
                    });
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Failed to fetch payment history.', 'error');
                }
            });

        });
        $(document).ready(function () {
            let branchId = localStorage.getItem('selectedBranchId');

            // Load years in filter
            $.ajax({
                url: '/api/daily-data/filters/years',
                type: 'GET',

                success: function (years) {
                    $('#filterYear').empty();
                    $('#filterYear').append('<option value="">All</option>'); // always first
                    years.forEach(function (year) {
                        $('#filterYear').append(`<option value="${year}">${year}</option>`);
                    });
                    $('#filterYear').trigger('change'); // refresh select2
                }
            });

            $('#filterPatient').select2({
                placeholder: "-- Select Patient --",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '/api/patients',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: function (params) {
                        return {
                            search: params.term // search term sent to API
                        };
                    },
                    processResults: function (data) {
                        // Map patients
                        let results = data.patients.map(p => {
                            let name = p.fullname ?
                                p.fullname.charAt(0).toUpperCase() + p.fullname.slice(1) :
                                '';
                            return {
                                id: p.id,
                                text: name
                            };
                        });

                        // Always add "All" at the top
                        results.unshift({
                            id: "",
                            text: "All"
                        });

                        return {
                            results: results
                        };
                    },
                    cache: true
                }
            });


            function ucfirst(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
            }

            // 🔹 Load Daily Data
            function loadDailyData() {
                const year = $('#filterYear').val();
                const month = $('#filterMonth').val();
                const date = $('#filterDate').val();
                const patientId = $("#filterPatient").val();

                $.ajax({
                    url: '/api/daily-data',
                    type: 'GET',
                    data: {
                        year,
                        month,
                        date,
                        patient_id: patientId,
                        branch_id: branchId
                    },
                    success: function (data) {
                        let userRoleId = {{ Auth::user()->role_id }};
                        if ($.fn.DataTable.isDataTable("#daily_datatbl")) {
                            $('#daily_datatbl').DataTable().destroy();
                        }
                        let tableBody = $("#daily_dataTableBody");
                        tableBody.empty();

                        let groupedData = {};
                        let monthlyTotals = {};

                        data.data.forEach(function (entry) {
                            const date = entry.date;
                            const monthKey = date.substring(0, 7);

                            if (!groupedData[date]) {
                                groupedData[date] = {
                                    totalAmount: 0,
                                    receivedAmount: 0,
                                    pendingAmount: 0,
                                    entries: []
                                };
                            }
                            groupedData[date].entries.push(entry);
                            groupedData[date].totalAmount += parseFloat(entry.amount || 0);
                            groupedData[date].receivedAmount += parseFloat(entry.received || 0);
                            groupedData[date].pendingAmount += parseFloat(entry.pending || 0);

                            if (!monthlyTotals[monthKey]) {
                                monthlyTotals[monthKey] = {
                                    totalAmount: 0,
                                    receivedAmount: 0,
                                    pendingAmount: 0
                                };
                            }
                            monthlyTotals[monthKey].totalAmount += parseFloat(entry.amount ||
                                0);
                            monthlyTotals[monthKey].receivedAmount += parseFloat(entry
                                .received || 0);
                            monthlyTotals[monthKey].pendingAmount += parseFloat(entry.pending ||
                                0);
                        });

                        Object.keys(groupedData).forEach(function (date) {
                            let group = groupedData[date];
                            let groupId = `group-${date.replaceAll('-', '')}`;
                            let today = new Date().toISOString().split("T")[0];
                            let addBtnHtml = "";
                            let amtColumn = "";

                            if (userRoleId == 1 || userRoleId == 2 || userRoleId == 4) {
                                amtColumn = `
                                                                    <span class="amount">Total: ₹ ${group.totalAmount.toFixed(2)}</span>
                                                                    <span class="amount">Received: ₹ ${group.receivedAmount.toFixed(2)}</span>
                                                                    <span class="amount">Pending: ₹ ${group.pendingAmount.toFixed(2)}</span>
                                                                    <button class="btn btn-rounded exportButton" style="background-color:#fed9cf;">
                                                                        <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                                                                    </button>
                                                                `;
                            }
                            @if (app('hasPermission')(30, 'create'))
                                if (date >= today) {
                                    addBtnHtml = `
                                                                                                            <a href="{{ route('daily_data.create') }}" class="btn btn-rounded btn-hdr" style="background-color:#fed9cf;">
                                                                                                                <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                                                                                            </a>`;
                                }
                            @endif

                            // 🔹 Wrap export + add buttons inside a flex container for mobile
                            let actionBtnsContainer = addBtnHtml ? `<div class="daily-action-btns d-flex flex-wrap justify-content-center align-items-center gap-2 mt-2 mt-md-0">${addBtnHtml}</div>` : '';
                            let groupRow = `
                                                                <tr class="group-row" style="background:#f8f9fa; font-weight:bold;">
                                                                    <td colspan="10">
                                                                        <div class="d-flex justify-content-between align-items-center flex-wrap px-3 py-2">
                                                                            <div>
                                                                                <i class="fa fa-caret-right toggle-group" data-target="${groupId}" style="cursor:pointer; padding-right:10px;"></i>
                                                                                ${date}
                                                                            </div>
                                                                            <div class="summary d-flex flex-wrap justify-content-end align-items-center gap-2 text-nowrap">
                                                                                ${amtColumn}
                                                                                ${actionBtnsContainer}
                                                                            </div>
                                                                        </div>
                                                                        <div class="child-table-container ${groupId} d-none" style="padding: 10px; background: #fff; width: 100%;">
                                                                            <table class="table table-bordered group-table w-100 mb-0">
                                                                            <thead style="background-color:#f0f0f0; font-weight:bold;">
                                                                                <tr>
                                                                                    <th>Patient Name</th>
                                                                                    <th class="d-none d-md-table-cell">Treatment</th>
                                                                                    <th class="d-none d-md-table-cell">Date</th>
                                                                                    <th class="d-none d-md-table-cell">Total Amount</th>
                                                                                    <th class="d-none d-md-table-cell">Pending</th>
                                                                                    <th class="d-none d-md-table-cell">Status</th>
                                                                                       @if($razorpayEnabled)
                                                                                        <th class="d-none d-md-table-cell">Payment Link</th>

                                                                                       @endif

                                                                                    <th class="d-none d-md-table-cell">Action</th>
                                                                                    <th class="d-table-cell d-md-none">Details</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                            `;

                            group.entries.forEach(function (entry) {
                                groupRow += `
                                                                    <tr>
                                                                        <td class="view-daily_data" data-id="${entry.id}" style="cursor:pointer">
                                                                            ${entry.patient_name ? ucfirst(entry.patient_name) : 'N/A'}
                                                                        </td>
                                                                        <td class="d-none d-md-table-cell">${entry.treatment_name || 'N/A'}</td>
                                                                        <td class="d-none d-md-table-cell">${entry.date || 'N/A'}</td>
                                                                        <td class="d-none d-md-table-cell"> ${entry.amount ?? 'N/A'}</td>
                                                                        <td class="d-none d-md-table-cell"> ${entry.pending ? parseFloat(entry.pending).toFixed(2) : 'N/A'}</td>
                                                                        <td class="d-none d-md-table-cell">
                                                                            ${entry.status === 'pending'
                                        ? `<button class="btn btn-sm btn-outline-primary make-payment-btn" data-id="${entry.id}" style="font-size:11px;font-weight:600;">
                                                                                                                                                    <i class="fa fa-credit-card"></i> Make Payment
                                                                                                                                                  </button>`
                                        : entry.status ? ucfirst(entry.status) : 'N/A'}
                                                                        </td>
                                                                           @if($razorpayEnabled)
                                                                            <td class="d-none d-md-table-cell">
                                                                                <button class="btn btn-sm btn-outline-success generate-link-btn"
                                                                                    data-id="${entry.id}"
                                                                                    data-pending="${entry.pending}">
                                                                                    <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                                                                                </button>
                                                                            </td>
                                                                           @endif


                                                                        <td class="d-none d-md-table-cell" style="padding: .5rem !important;">
                                                                            <div class="icon">
                                                                                @if (app('hasPermission')(30, 'update'))
                                                                                    <i class="fa fa-pencil m-r-5 icon1 edit-daily_data" style="cursor:pointer" data-id="${entry.id}" title="Edit Daily register"></i>
                                                                                @endif
                                                                                @if (app('hasPermission')(30, 'delete'))
                                                                                    <i class="fa fa-trash-o m-r-5 icon2 delete-daily_data" style="cursor:pointer" data-id="${entry.id}" title="Delete Daily register"></i>
                                                                                @endif
                                                                                <i class="fa fa-credit-card m-r-5 icon3 payment-history" style="cursor:pointer; color:white;" title="Payment History" data-id="${entry.id}"></i>
                                                                                <i class="fa fa-download m-r-5 icon4  download-daily-pdf"  style="cursor:pointer; color:white;" title="Download PDF" data-id="${entry.id}"> </i>
                                                                            </div>
                                                                        </td>
                                                                        <td class="d-table-cell d-md-none text-center">
                                                                            <button class="btn btn-sm btn-light expand-btn" style="border:1px solid #ccc;">
                                                                                <i class="fa fa-chevron-down"></i>
                                                                            </button>
                                                                        </td>

                                                                    </tr>
                                                                `;
                            });

                            groupRow += `</tbody></table></div></td></tr>`;
                            tableBody.append(groupRow);
                        });




                        // 🔹 Monthly Summary
                        $("#monthly-summary").empty();
                        let grandTotal = 0;
                        let grandReceived = 0;
                        let grandPending = 0;

                        Object.keys(monthlyTotals).forEach(function (monthKey) {

                            // Optional month filter
                            if (month && monthKey.split("-")[1] !== month.padStart(2, '0')) {
                                return;
                            }

                            let totals = monthlyTotals[monthKey];

                            grandTotal += totals.totalAmount;
                            grandReceived += totals.receivedAmount;
                            grandPending += totals.pendingAmount;

                        });
                        $("#monthly-summary").append(`
                                                                <div class="d-flex justify-content-center align-items-center w-100">
                                                                    <div class="alert alert-info"
                                                                        style="background:transparent; font-weight:bold; border:1px solid #cfece0; color:black; border-radius:50px !important; font-size:14px; padding:10px 26px; width:auto; max-width:100%;">
                                                                        <div style="display:flex; gap:15px; flex-wrap: wrap; justify-content: center;">
                                                                            <span class="amount" style="white-space: nowrap;">Total: ₹ ${grandTotal.toFixed(2)}</span>
                                                                            <span class="amount" style="white-space: nowrap;">Received: ₹ ${grandReceived.toFixed(2)}</span>
                                                                            <span class="amount" style="white-space: nowrap;">Pending: ₹ ${grandPending.toFixed(2)}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            `);

                        $('#daily_datatbl').DataTable({
                            paging: true,
                            searching: true,
                            ordering: false
                        });
                    },
                    error: xhr => console.log(xhr.responseText)
                });
            }

            loadDailyData();


            // Filters change
            $('#filterYear, #filterMonth, #filterDate, #filterPatient').on('change', function () {
                loadDailyData();
            });

            // razorpay

            $(document).on('click', '.generate-link-btn', function () {

                let dailyId = $(this).data('id');
                let pendingAmount = $(this).data('pending');

                if (!pendingAmount || pendingAmount <= 0) {
                    Swal.fire('Info', 'No pending amount for payment.', 'info');
                    return;
                }

                $.ajax({
                    url: `/api/razorpay/generate-payment-link`,
                    type: 'POST',
                    data: {
                        daily_id: dailyId,
                        amount: pendingAmount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        // Copy link
                        navigator.clipboard.writeText(res.payment_link);

                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Link Generated',
                            html: `
                                                            <p><strong>Patient Name:</strong> ${res.patient_name ?? '-'}</p>
                                                            <p><strong>Contact No:</strong> ${res.patient_phone ?? '-'}</p>
                                                            <p><strong>Amount:</strong> ₹${pendingAmount}</p>

                                                            <input type="text" class="swal2-input" value="${res.payment_link}" readonly>

                                                            <small>Link copied to clipboard ✔</small>
                                                        `,
                            showConfirmButton: false, // Hide OK button
                            showCancelButton: true, // Show only cancel button
                            cancelButtonText: 'Close',
                            cancelButtonColor: '#f89884'
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                location.reload(); // Reload page when Cancel clicked
                            }
                        });
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message ||
                            'Failed to generate payment link';

                        Swal.fire({
                            icon: xhr.status === 403 ? 'warning' : 'error',
                            title: xhr.status === 403 ? 'Razorpay Disabled' : 'Error',
                            text: message,
                            showConfirmButton: false, // hide OK button
                            showCancelButton: true, // show Cancel button instead
                            cancelButtonText: 'Close',
                            cancelButtonColor: '#f89884',
                            customClass: {
                                cancelButton: 'swal-cancel-btn' // apply your custom style
                            }
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                location.reload(); // Reload page when Cancel clicked
                            }
                        });
                    }


                });
            });




            // Delete
            $(document).on('click', '.delete-daily_data', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete this daily entry?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        htmlContainer: 'custom-swal-text',
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/daily-data/${id}`,
                            type: 'DELETE',
                            success: function (res) {
                                Swal.fire('Deleted!', res.message, 'success');
                                location.reload();
                            },
                            error: function (xhr) {
                                Swal.fire('Error', 'Failed to delete entry.', 'error');
                            }
                        });
                    }
                });
            });

            // Mobile Dropdown Expand for Daily Data
            // ✅ Mobile Dropdown Expand/Collapse (supports multiple rows)
            $(document).on("click", ".expand-btn", function (e) {
                e.stopPropagation();

                const btn = $(this);
                const icon = btn.find("i");
                const tr = btn.closest("tr");
                const existingRow = tr.next(".details-row");
                const table = tr.closest("table");

                // Reference header (first + last th)
                const headerRow = table.find("thead tr").first();
                const patientHeader = headerRow.find("th").first();
                const detailsHeader = headerRow.find("th:last");

                // --- If already open → close only this row ---
                if (existingRow.length) {
                    existingRow.slideUp(200, function () {
                        $(this).remove();
                    });

                    // Reset caret direction only
                    icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
                    return;
                }

                // --- Always make sure headers have colspan ---
                patientHeader.attr("colspan", "5");
                detailsHeader.attr("colspan", "5");

                // --- Add colspan=5 to ALL rows’ first and last cells ---
                table.find("tbody tr").each(function () {
                    const row = $(this);
                    row.find("td:first").attr("colspan", "5");
                    row.find("td:last").attr("colspan", "5");
                });

                // --- Extract data from hidden desktop columns ---
                const treatment = tr.find(".d-none.d-md-table-cell").eq(0).text().trim() || "N/A";
                const date = tr.find(".d-none.d-md-table-cell").eq(1).text().trim() || "N/A";
                const totalAmount = tr.find(".d-none.d-md-table-cell").eq(2).text().trim() || "0.00";
                const pending = tr.find(".d-none.d-md-table-cell").eq(3).text().trim() || "0.00";
                const status = tr.find(".d-none.d-md-table-cell").eq(4).html() || "N/A";
                const collectedBy = tr.find(".d-none.d-md-table-cell").eq(5).text().trim() || "N/A";
                const comments = tr.find(".d-none.d-md-table-cell").eq(6).text().trim() || "N/A";
                const actions =
                    tr.find(".icon").prop("outerHTML") ||
                    '<span class="text-muted">No Actions</span>';
                const generateLinkBtn = tr.find(".generate-discharge-link-btn").prop("outerHTML") ||
                    tr.find(".generate-link-btn").prop("outerHTML") ||
                    '<span class="text-muted">No Payment Link</span>';

                // --- Build details row ---
                const detailsRow = $(`
                                            <tr class="details-row">
                                                <td colspan="10">
                                                    <div class="details-content" style="padding:10px;">
                                                        <div><strong>Treatment:</strong> ${treatment}</div>
                                                        <div><strong>Date:</strong> ${date}</div>
                                                        <div><strong>Total Amount:</strong> ₹${totalAmount}</div>
                                                        <div><strong>Pending:</strong> ₹${pending}</div>
                                                        <div><strong>Status:</strong> ${status}</div>
                                                        <div><strong>Collected By:</strong> ${collectedBy}</div>
                                                        <div><strong>Comments:</strong> ${comments}</div>
                                                        <div class="mt-2">

                                    <strong>Payment Link:</strong><br>

                                    ${generateLinkBtn}

                                </div>
                                                        <!-- 🔹 Actions horizontally aligned -->
                                                        <div class="action-row"
                                                             style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-top:10px;">
                                                            <strong>Actions:</strong>
                                                            <div class="action-icons"
                                                                 style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
                                                                 ${actions}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        `);

                // --- Insert and animate open ---
                tr.after(detailsRow.hide());

                // Make sure all details rows have colspan=10
                table.find(".details-row td").attr("colspan", "10");

                detailsRow.slideDown(200);

                // Change caret direction
                icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
            });








            // View & Edit
            $(document).on('click', '.view-daily_data', function () {
                let id = $(this).data('id');
                window.location.href = `/daily_data/show/${id}`;
            });

            $(document).on('click', '.edit-daily_data', function () {
                let id = $(this).data('id');
                window.location.href = `/daily_data/edit/${id}`;
            });

            $(document).on('click', '.download-daily-pdf', function () {
                const dailyId = $(this).data('id');
                window.open(`/api/daily-data/${dailyId}/pdf?branch_id=${branchId}`, '_blank');
            });




            $(document).on('click', '.payment-history', function () {
                let dailyId = $(this).data('id');

                $.ajax({
                    url: `/api/daily-data/${dailyId}/payment-history`,
                    method: 'GET',
                    success: function (response) {
                        if (!response.history || response.history.length === 0) {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Payment History',
                                text: 'No records found for this entry.'
                            });
                            return;
                        }

                        // Build table HTML
                        let tableHTML = `
                                                            <p><strong>Payable:</strong> ${response.payable}</p>
                                                            <p><strong>Remaining:</strong> ${response.remaining}</p>
                                                            <table class="table table-bordered" style="width:100%; font-size:14px;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Paid Amount</th>
                                                                        <th>Method</th>
                                                                         <th>Collect By</th>
                                                                          <th>Comment</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                        `;

                        response.history.forEach(item => {

                            let dateOnly = '';
                            if (item.payment_date) {
                                dateOnly = new Date(item.payment_date).toISOString()
                                    .split(
                                        'T')[0];
                            } else if (item.created_at) {
                                dateOnly = new Date(item.created_at).toISOString()
                                    .split(
                                        'T')[0];
                            }
                            tableHTML += `
                                                                <tr>
                                                                    <td>${dateOnly}</td>
                                                                    <td>${item.paid_amount ?? ''}</td>
                                                                    <td>${item.payment_mode ?? ''}</td>
                                                                   <td>${response.collected_by ?? ''}</td>
                    <td>${response.comment ?? ''}</td>

                                                                </tr>
                                                            `;
                        });

                        tableHTML += `</tbody></table>`;

                        Swal.fire({
                            title: 'Payment History',
                            html: tableHTML,
                            width: '800px',
                            confirmButtonText: 'Close'
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to fetch payment history.'
                        });
                    }
                });
            });



        });
    </script>
@endsection


