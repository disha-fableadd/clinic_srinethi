@extends('layout.app')
<style>
    div:where(.swal2-container) input:where(.swal2-input):focus,
    div:where(.swal2-container) input:where(.swal2-file):focus,
    div:where(.swal2-container) textarea:where(.swal2-textarea):focus {

        box-shadow: none !important;
    }

    div:where(.swal2-container) .swal2-input {
        height: auto !important;
        padding: 8px !important;
        width: 100% !important;
        margin: 0 !important;
        border: 1px solid #f89884 !important;
        border-radius: 50px !important;
    }

    colgroup {
        display: none;
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

    .card-header {
        background-color: #f89884 !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
    }

    @media (max-width: 767px) {
        .card-header .card-title {
            font-size: 15px !important;
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .card-header .btn {
            font-size: 14px !important;
            padding: 5px 10px;
        }

        .card-header .btn+.btn {
            margin-left: 5px;
        }
    }

    @media (max-width: 375px) {
        .card-header .card-title {
            font-size: 13px !important;
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .card-header .btn {
            float: none !important;
            display: inline-block;
            font-size: 12px !important;
            padding: 5px 8px;
            margin: 3px 2px;
        }

        .card-header {
            text-align: center;
        }
    }

    /* Add these styles for responsive details */
    .details-row {
        background-color: #f8f9fa;
        display: none;
    }

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }

    .expand-btn {
        border: none;
        background: none;
        color: #f89884;
        font-size: 16px;
        cursor: pointer;
    }

    button.btn.btn-link.expand-btn {
        background: #f89884;
        color: white;
        padding: 7px;
        border-radius: 9px;
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

    .btn-outline-success:hover {
        color: black !important;
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
    }

    .btn-outline-success {
        color: black !important;

        border-color: #cfece0 !important;
    }
</style>

@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
@endphp

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-calendar-check-o px-2" style="font-size:20px"></i> All Booking Details
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(31, 'create'))
                                <a href="{{ route('treatment_booking.create') }}" class="btn btn-rounded btn-hdr">
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
                                <div class="col-md-3 col-sm-6 col-6 mt-2">
                                    <label>Date</label>
                                    <input type="date" id="filterDate" class="form-control filter" />
                                </div>
                                <div class="col-md-3 col-sm-6 col-6 mt-2">
                                    <label>Patient</label>
                                    <select id="filterPatient" class="form-control filter">
                                        <option value="">All</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="treatmenttbl" class="table">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Treatment</th>
                                            <th class="d-none d-md-table-cell">Machine</th>
                                            <th class="d-none d-md-table-cell">Plan</th>
                                            <th class="d-none d-md-table-cell">Amount</th>
                                            <th class="d-none d-md-table-cell">Pending</th>
                                            <th class="d-none d-md-table-cell">Payment Status</th>
                                            {{-- <th class="d-none d-md-table-cell">Payment Date</th> --}}
                                               @if($razorpayEnabled)
                                            <th class="d-none d-md-table-cell">Payment Link</th>
                                               @endif
                                            <th class="d-none d-md-table-cell">Actions</th>

                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="treatmentTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
    $(document).ready(function() {
        let branchId = localStorage.getItem('selectedBranchId');
        let treatmentTable;

        if (!branchId) {
            Swal.fire('Error', 'Branch not selected!', 'error');
            return;
        }

        function capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // --- Load Years Dynamically ---
        $.ajax({
            url: '/api/treatment-booking/filters/years',
            type: 'GET',
            success: function(years) {
                $('#filterYear').empty().append('<option value="">All</option>');
                years.forEach(function(year) {
                    $('#filterYear').append(`<option value="${year}">${year}</option>`);
                });
                $('#filterYear').trigger('change');
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });

        // --- Initialize Select2 for Filters ---
        $('.yearfilter').select2({
            width: '100%',
            placeholder: "Select Year",
            allowClear: true,
            minimumResultsForSearch: Infinity
        });

        $('.selectmonth').select2({
            placeholder: "Select Month",
            allowClear: true,
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        $('#filterPatient').select2({
            placeholder: "-- Select Patient --",
            allowClear: true,
            ajax: {
                url: '/api/treatment-booking/patients',
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        branch_id: branchId
                    };
                },
                processResults: function(data) {
                    let results = data.patients.map(patient => {
                        let name = patient.fullname ?
                            patient.fullname.charAt(0).toUpperCase() + patient.fullname.slice(1) :
                            '';
                        return {
                            id: patient.id,
                            text: name
                        };
                    });
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

        function buildStatusHtml(booking) {
            if (booking.status && booking.status.toLowerCase() === 'pending') {
                return `
                    <button class="btn btn-sm make-payment-btn" style="background-color:#cfece0;font-weight: 600;" data-id="${booking.id}">
                        <i class="fa fa-credit-card"></i> Make Payment
                    </button>
                `;
            }
            return `<span>${capitalize(booking.status || '')}</span>`;
        }

        function buildActionsHtml(booking) {
            let icons = '<div class="icon" style="cursor:pointer">';
            @if (app('hasPermission')(7, 'view'))
                icons += `<i class="fa fa-eye m-r-5 icon3 view-treatment" data-id="${booking.id}" title="View"></i>`;
            @endif
            @if (app('hasPermission')(7, 'update'))
                icons += `<i class="fa fa-pencil m-r-5 icon1 edit-treatment" data-id="${booking.id}" title="Edit"></i>`;
            @endif
            @if (app('hasPermission')(7, 'delete'))
                icons += `<i class="fa fa-trash-o m-r-5 icon2 delete-treatment-booking" data-id="${booking.id}" title="Delete"></i>`;
            @endif
            icons += `<i class="fa fa-download m-r-5 icon4 download-treatment-booking" data-id="${booking.id}" title="Download PDF"></i>`;
            icons += '</div>';
            return icons;
        }
        window.treatmentCapitalize = capitalize;
        window.treatmentBuildStatusHtml = buildStatusHtml;
        window.treatmentBuildActionsHtml = buildActionsHtml;

        function initTreatmentTable() {
            if ($.fn.DataTable.isDataTable('#treatmenttbl')) {
                try {
                    let existingTable = $('#treatmenttbl').DataTable();
                    existingTable.destroy();
                    $('#treatmenttbl').removeClass('dataTable');
                    $('#treatmenttbl tbody').empty();
                    $.removeData($('#treatmenttbl')[0], 'DataTable');
                    $.removeData($('#treatmenttbl')[0], 'DataTables_DataTable');
                    $('#treatmenttbl').off();
                } catch (e) {
                    $('#treatmenttbl tbody').empty();
                    $.removeData($('#treatmenttbl')[0]);
                }
            }

            if ($('#treatmenttbl tbody').length === 0) {
                $('#treatmenttbl').append('<tbody id="treatmentTableBody"></tbody>');
            }

            setTimeout(function() {
                treatmentTable = $('#treatmenttbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/treatment_booking',
                            type: 'GET',
                            data: {
                                branch_id: branchId,
                                year: $('#filterYear').val(),
                                month: $('#filterMonth').val(),
                                date: $('#filterDate').val(),
                                patient_id: $('#filterPatient').val(),
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            success: function(res) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: res.pagination?.total || 0,
                                    recordsFiltered: res.pagination?.total || 0,
                                    data: res.data || []
                                });
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                            }
                        });
                    },
                    columns: [
                        {
                            data: 'patient_name',
                            render: data => capitalize(data || 'N/A'),
                            className: 'view-treatment',
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'treatment_name',
                            render: data => capitalize(data || 'N/A'),
                            className: 'view-treatment',
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'machine_name',
                            render: data => capitalize(data || 'N/A'),
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: 'plan',
                            render: data => capitalize(data || ''),
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: 'amount',
                            render: data => data || '0.00',
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: 'pending',
                            render: data => data || '0.00',
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return buildStatusHtml(row);
                            },
                            className: 'd-none d-md-table-cell payment-status-cell'
                        },
                        @if($razorpayEnabled)
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-sm btn-outline-success generate-link-btn"
                                        data-id="${row.id}"
                                        data-pending="${row.pending}">
                                        <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                                    </button>
                                `;
                            },
                            className: 'd-none d-md-table-cell'
                        },
                        @endif
                        {
                            data: null,
                            render: function(data, type, row) {
                                return buildActionsHtml(row);
                            },
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-link expand-btn" data-id="${row.id}">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
                                `;
                            },
                            className: 'd-table-cell d-md-none text-center',
                            orderable: false
                        }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 10
                });
                window.treatmentTable = treatmentTable;
            }, 50);
        }

        function reloadTreatmentTable() {
            if (!treatmentTable) {
                initTreatmentTable();
                return;
            }
            treatmentTable.ajax.reload(null, false);
        }

        initTreatmentTable();

        // Filter change event
        $('#filterYear, #filterMonth, #filterDate, #filterPatient').on('change', function() {
            reloadTreatmentTable();
        });

        // Export button
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem("selectedBranchId");
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('treatment_booking.export') }}" + "?branch_id=" + branchId;
        });

        // View treatment
        $(document).on('click', '.view-treatment', function() {
            var treatmentId = $(this).data('id');
            if (!treatmentId) return;
            window.location.href = '/treatment_booking/show/' + treatmentId;
        });

        // Edit treatment
        $(document).on('click', '.edit-treatment', function() {
            var treatmentId = $(this).data('id');
            if (!treatmentId) return;
            window.location.href = '/treatment_booking/edit/' + treatmentId;
        });

        // Download treatment booking
        $(document).on('click', '.download-treatment-booking', function() {
            const treatmentId = $(this).data('id');
            window.open(`/api/treatment-booking/${treatmentId}/pdf?branch_id=${branchId}`, '_blank');
        });

        // Delete treatment booking
        $(document).on('click', '.delete-treatment-booking', function() {
            var treatmentId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete treatment booking!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#cfece0',
                cancelButtonColor: '#f89884',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/treatment_booking/' + treatmentId,
                        type: 'DELETE',
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        data: {
                            branch_id: branchId
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Treatment booking deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(reloadTreatmentTable);
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to delete treatment. Please try again.',
                                'error');
                        }
                    });
                }
            });
        });

        // Razorpay generate link
        $(document).on('click', '.generate-link-btn', function() {
            let bookingId = $(this).data('id');
            let pendingAmount = $(this).data('pending');

            if (!pendingAmount || pendingAmount <= 0) {
                Swal.fire('Info', 'No pending amount for payment.', 'info');
                return;
            }

            $.ajax({
                url: `/api/treatment_booking/generate-payment-link-treatment`,
                type: 'POST',
                data: {
                    booking_id: bookingId,
                    amount: pendingAmount,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    navigator.clipboard.writeText(res.payment_link);

                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Link Generated',
                        html: `
                            <p><strong>Patient Name:</strong> ${res.patient_name ?? '-'}</p>
                            <p><strong>Contact No:</strong> ${res.patient_phone ?? '-'}</p>
                            <p><strong>Amount:</strong> â‚¹${pendingAmount}</p>
                            <input type="text" class="swal2-input" value="${res.payment_link}" readonly>
                            <small>Link copied to clipboard âœ”</small>
                        `,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        cancelButtonColor: '#f89884'
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            reloadTreatmentTable();
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to generate payment link', 'error');
                }
            });
        });

        // Make Payment handler - works for both desktop and mobile views
        $(document).on('click', '.make-payment-btn', function(e) {
            e.stopPropagation();
            const bookingId = $(this).data('id');

            $.ajax({
                url: `/api/treatment_booking/${bookingId}/payment-history`,
                method: 'GET',
                data: {
                    branch_id: branchId
                },
                success: function(response) {
                    const history = response.history || [];
                    const remainAmount = response.remaining || 0;
                    const payableAmount = response.payable || 0;

                    let historyHtml = '';
                    if (history.length > 0) {
                        history.forEach(function(payment) {
                            let paymentDate = new Date(payment.created_at);
                            let formattedDate = paymentDate.toLocaleString('en-IN', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            historyHtml += `
                                <div style="border:1px solid #f89884; padding:8px; border-radius:5px; margin-bottom:5px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <div style="font-weight:500; font-size:14px; color:#666;">${formattedDate}</div>
                                        <div style="font-weight:700; font-size:14px; text-align:right;">
                                            â‚¹${payment.amount} via ${payment.payment_type}
                                        </div>
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
                                    <span style="font-size:14px; color:#666;">Payable: â‚¹${payableAmount}</span>
                                </div>`,
                        html: `
                            <hr>
                            <div style="text-align:left; max-height:400px; overflow-y:auto;">
                                <h6 style="margin:0; margin-bottom:5px; font-weight:bold; font-size:14px;">Previous Payments</h6>
                                ${historyHtml}
                                <label style="margin-top:10px;font-size:14px; font-weight:bold;">Remaining Balance</label>
                                <input type="text" class="swal2-input" id="remain_balance" style="font-size:14px;" value="${remainAmount}" readonly>
                                <label style="margin-top:10px;font-size:14px; font-weight:bold;">Payment Method</label>
                                <select id="payment_mode" style="font-size:14px;" class="swal2-input">
                                    <option value="">Select</option>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="cash+online">Cash + Online</option>
                                </select>
                                <div id="amountFields">
                                    <label style="margin-top:10px;font-size:14px; font-weight:bold;">Amount to Pay</label>
                                    <input type="number" class="swal2-input" id="paid_amount" style="font-size:14px;" placeholder="Enter amount">
                                </div>
                            </div>
                        `,
                        didOpen: () => {
                            const paymentModeSelect = document.getElementById('payment_mode');
                            const amountFields = document.getElementById('amountFields');

                            paymentModeSelect.addEventListener('change', function() {
                                if (this.value === 'cash+online') {
                                    amountFields.innerHTML = `
                                        <label style="margin-top:10px;font-size:14px; font-weight:bold;">Cash Amount</label>
                                        <input type="number" class="swal2-input" id="cash_amount" style="font-size:14px;" placeholder="Enter cash amount">
                                        <label style="margin-top:10px;font-size:14px; font-weight:bold;">Online Amount</label>
                                        <input type="number" class="swal2-input" id="online_amount" style="font-size:14px;" placeholder="Enter online amount">
                                    `;
                                } else {
                                    amountFields.innerHTML = `
                                        <label style="margin-top:10px;font-size:14px; font-weight:bold;">Amount to Pay</label>
                                        <input type="number" class="swal2-input" id="paid_amount" style="font-size:14px;" placeholder="Enter amount">
                                    `;
                                }
                            });
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Submit Payment',
                        cancelButtonText: 'Close',
                        focusConfirm: false,
                        preConfirm: () => {
                            const mode = document.getElementById('payment_mode').value;
                            const remaining = parseFloat(remainAmount);

                            if (!mode) {
                                Swal.showValidationMessage('Please select a payment method.');
                                return false;
                            }

                            if (mode === 'cash+online') {
                                const cash = parseFloat(document.getElementById('cash_amount').value) || 0;
                                const online = parseFloat(document.getElementById('online_amount').value) || 0;
                                const total = cash + online;

                                if (total <= 0) {
                                    Swal.showValidationMessage('Enter a valid Cash or Online amount.');
                                    return false;
                                }
                                if (total > remaining) {
                                    Swal.showValidationMessage(`Total payment cannot exceed â‚¹${remaining}.`);
                                    return false;
                                }
                                return {
                                    paid_amount: total,
                                    payment_mode: mode,
                                    cash: cash,
                                    online: online
                                };
                            } else {
                                const amount = parseFloat(document.getElementById('paid_amount').value);
                                if (!amount || amount <= 0) {
                                    Swal.showValidationMessage('Please enter a valid amount.');
                                    return false;
                                }
                                if (amount > remaining) {
                                    Swal.showValidationMessage(`Payment amount cannot exceed â‚¹${remaining}.`);
                                    return false;
                                }
                                return {
                                    paid_amount: amount,
                                    payment_mode: mode
                                };
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/api/treatment_booking/${bookingId}/make-payment`,
                                method: 'POST',
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                data: {
                                    paid_amount: result.value.paid_amount,
                                    payment_mode: result.value.payment_mode,
                                    cash: result.value.cash || 0,
                                    online: result.value.online || 0,
                                    branch_id: branchId
                                },
                                success: function(res) {
                                    Swal.fire('Success', res.message, 'success');
                                    reloadTreatmentTable();
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message || 'Payment failed.', 'error');
                                }
                            });
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch payment history.', 'error');
                }
            });
        });
    });

    // Mobile expand
    $(document).on('click', '.expand-btn', function(e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find('i');
        const tr = btn.closest('tr');
        const existingRow = tr.next('.details-row');

        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass('fa-chevron-down fa-chevron-up');
            return;
        }

        if (!window.treatmentTable || !$.fn.DataTable.isDataTable('#treatmenttbl')) {
            return;
        }

        const booking = window.treatmentTable.row(tr).data() || {};

        let statusHtml = window.treatmentBuildStatusHtml(booking);
        let paymentLinkBtn = `
            <button class="btn btn-sm btn-outline-success generate-link-btn mt-2"
                data-id="${booking.id}"
                data-pending="${booking.pending}">
                <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
            </button>
        `;

        let icons = window.treatmentBuildActionsHtml(booking);

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="@if($razorpayEnabled) 10 @else 9 @endif">
                    <div class="details-content">
                        <div><strong>Machine:</strong> ${window.treatmentCapitalize(booking.machine_name || 'N/A')}</div>
                        <div><strong>Plan:</strong> ${window.treatmentCapitalize(booking.plan || '')}</div>
                        <div><strong>Amount:</strong> â‚¹${booking.amount || '0.00'}</div>
                        <div><strong>Pending:</strong> â‚¹${booking.pending || '0.00'}</div>

                        <div class="payment-status-mobile" data-id="${booking.id}">
                            <strong>Status:</strong> ${statusHtml}
                        </div>

                        <div><strong>Payment Date:</strong> ${booking.payment_date || 'N/A'}</div>
                        @if($razorpayEnabled)
                        <div class="mt-2"><strong>Payment Link:</strong><br>
                            ${paymentLinkBtn}
                        </div>
                        @endif
                        <div class="mt-2">
                            <strong>Actions:</strong><br>
                            ${icons}
                        </div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass('fa-chevron-down fa-chevron-up');
    });
</script>
@endsection

