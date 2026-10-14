@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH') . 'admin/assets/css/appointment-index.css') }}">
<style>
    .btn-outline-success:hover {
        color: black !important;
        background-color: #cfece0 !important;
        border-color: #cfece0 !important;
    }

    .btn-outline-success {
        color: black !important;

        border-color: #cfece0 !important;
    }

    .swal-confirm-btn {
        color: #000 !important;
        /* BLACK text */
    }

    .swal-cancel-btn {
        color: white !important;
        border-radius: 23px !important;
    }
</style>

@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
    $isPatientRole = optional(Auth::user()?->role)->name === 'Patient';
@endphp

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white"><i class="fa fa-calendar px-2"
                                    style="font-size:20px"></i>Appointment </h3>



                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(6, 'create') && !$isPatientRole)
                                <a href="{{ route('appointment.create') }}" class="btn btn-rounded btn-hdr"><i
                                        class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>

                        <div class="card-body ">
                            @php
                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                            @endphp
                            @if($currentProjectTypeId !== 3)
                            <div class="col-6 mt-4">
                                <div>
                                    <select id="statusFilter" class="form-control" style="">
                                        <option value="all">All Appointment</option>
                                        <option value="upcoming">Upcoming</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="follow-up">Follow-up</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>




                                <table id="appointmenttable" class="table custom-table">
                                    <thead style="background-color:#F89884;" class="text-center">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th class="d-none d-md-table-cell">Treatment</th>
                                            <th class="d-none d-md-table-cell">Date</th>
                                            @if($currentProjectTypeId !== 3)
                                            <th class="d-none d-md-table-cell">Status</th>
                                            @endif
                                            @if ($razorpayEnabled)
                                                <th class="d-none d-md-table-cell">Payment Link</th>
                                            @endif

                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- Mobile-only column -->
                                        </tr>
                                    </thead>
                                    <tbody id="appointmentBody"></tbody>
                                </table>

                                @foreach ($appointments as $appointment)
                                    @include('components.followup-modal', [
                                        'appointment_id' => $appointment->id,
                                        'doctor_id' => $appointment->doctor_id,
                                        'patient_id' => $appointment->patient_id,
                                        'treatment_id' => $appointment->treatment_id,
                                        'module_type' => 'appointment',
                                    ])
                                    @include('components.reschedule-modal', [
                                        'appointment_id' => $appointment->id,
                                        'user_id' => $appointment->user_id,
                                        'module_type' => 'appointment',
                                    ])
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="invoiceForm">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#cfece0;">
                        <h5 class="modal-title" id="invoiceModalLabel">Create Invoice</h5>

                    </div>


                    <div class="modal-body">
                        <input type="hidden" name="appointment_id" id="appointment_id" />
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Patient</label>
                                    <input type="text" class="form-control" id="patient_name" readonly />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Doctor</label>
                                    <input type="text" class="form-control" id="doctor_name" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Treatment</label>
                                    <input type="text" class="form-control" id="treatment_name" readonly />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" name="date" id="invoice_date" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" name="price" id="price"
                                        min="0" step="0.01" required />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="instruction" class="form-label">Instruction (Optional)</label>
                                    <textarea class="form-control" name="instruction" id="instruction" rows="1"></textarea>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Generate Invoice</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="border-radius:50px">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>









    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        function getSelectedBranchId() {
            return localStorage.getItem('selectedBranchId') || '';
        }

        $('#exportButton').on('click', function() {
            let branchId = getSelectedBranchId();

            // Build export URL with branch_id
            let exportUrl = "{{ route('appointments.export') }}";
            if (branchId) {
                exportUrl += "?branch_id=" + branchId;
            }

            window.location.href = exportUrl; // Trigger download
        });

        // Handle expandable rows for mobile


        // let branchId = localStorage.getItem('selectedBranchId');
        $(document).ready(function() {
            const token = localStorage.getItem('token');
            let branchId = getSelectedBranchId();

            let appointmentTable;

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function formatDate(appointment) {
                let formattedDate = appointment?.date || 'N/A';
                if (appointment?.date) {
                    let dateTimeString = appointment.date;
                    const hasTime = /\d{2}:\d{2}/.test(appointment.date);
                    if (!hasTime && appointment.duration) {
                        const duration = appointment.duration.length === 5 ? appointment.duration + ':00' : appointment.duration;
                        dateTimeString = `${appointment.date}T${duration}`;
                    }

                    const date = new Date(dateTimeString);
                    if (!isNaN(date.getTime())) {
                        formattedDate = date.toLocaleString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                    }
                }
                return formattedDate;
            }

            function getStatusBadge(status) {
                let statusBadgeClass = 'badge-upcoming';
                switch ((status || '').toLowerCase()) {
                    case 'completed':
                        statusBadgeClass = 'badge-completed';
                        break;
                    case 'follow-up':
                        statusBadgeClass = 'badge-follow-up';
                        break;
                    case 'confirmed':
                        statusBadgeClass = 'badge-confirmed';
                        break;
                    case 'upcoming':
                        statusBadgeClass = 'badge-upcoming';
                        break;
                    case 'cancelled':
                        statusBadgeClass = 'badge-cancelled';
                        break;
                }
                return `<span class="custom-badge ${statusBadgeClass}">${status ? ucfirst(status.toLowerCase()) : 'N/A'}</span>`;
            }

            function buildActionsHtml(appointment) {
                let actionsHtml = `<div class="icon" style="cursor:pointer">`;
                @if (app('hasPermission')(6, 'update') && !$isPatientRole)
                    actionsHtml +=
                        `<i class="fas fa-clipboard-check m-r-5 icon2 followup-appointment" data-id="${appointment.id}" data-user_id="${appointment.user_id}" title="Follow-up Appointment"></i>`;
                @endif
                @if (app('hasPermission')(6, 'update') && !$isPatientRole)
                    actionsHtml +=
                        `<i class="fa fa-calendar-plus m-r-5 icon2 reschedule-appointment" data-id="${appointment.id}" title="Reschedule Appointment"></i>`;
                @endif
                @if (app('hasPermission')(6, 'view'))
                    actionsHtml +=
                        `<i class="fa fa-eye m-r-5 icon3 view-appointment" data-id="${appointment.id}" title="View"></i>`;
                @endif
                @if (app('hasPermission')(6, 'update') && !$isPatientRole)
                    actionsHtml +=
                        `<i class="fa fa-pencil m-r-5 icon1 edit-appointment" data-id="${appointment.id}" title="Edit"></i>`;
                @endif
                @if (app('hasPermission')(6, 'delete') && !$isPatientRole)
                    actionsHtml +=
                        `<i class="fa fa-trash-o m-r-5 icon2 delete-appointment" data-id="${appointment.id}" title="Delete"></i>`;
                @endif
                @if (!$isPatientRole)
                if (appointment.status !== 'completed') {
                    actionsHtml +=
                        `<i class="fas fa-check-circle m-r-5 icon2 complete-appointment" data-id="${appointment.id}" title="Complete"></i>`;
                }
                @endif
                actionsHtml +=
                    `<i class="fa fa-download m-r-5 download-appointment" data-id="${appointment.id}" title="Download"></i>`;
                actionsHtml += `</div>`;
                return actionsHtml;
            }

            function buildPaymentHtml(appointment) {
                @if ($razorpayEnabled)
                    return `
                        <button class="btn btn-sm btn-outline-success generate-appointment-link-btn"
                            data-id="${appointment.id}"
                            data-amount="${appointment.amount ?? 0}"
                            data-status="${appointment.payment_status ?? ''}">
                            <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                        </button>
                    `;
                @else
                    return '';
                @endif
            }

            function initializeAppointmentsTable() {
                if ($.fn.DataTable.isDataTable("#appointmenttable")) {
                    try {
                        let existingTable = $('#appointmenttable').DataTable();
                        existingTable.destroy();
                        $('#appointmenttable').removeClass('dataTable');
                        $('#appointmenttable tbody').empty();
                        $.removeData($('#appointmenttable')[0], 'DataTable');
                        $.removeData($('#appointmenttable')[0], 'DataTables_DataTable');
                        $('#appointmenttable').off();
                    } catch (e) {
                        $('#appointmenttable tbody').empty();
                        $.removeData($('#appointmenttable')[0]);
                    }
                }

                if ($('#appointmenttable tbody').length === 0) {
                    $('#appointmenttable').append('<tbody id="appointmentBody"></tbody>');
                }

                setTimeout(function() {
                    appointmentTable = $('#appointmenttable').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        ajax: function(data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            $.ajax({
                                url: '/api/appointments',
                                type: 'GET',
                                dataType: 'json',
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: data.length,
                                    search: data.search?.value || ''
                                },
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function(res) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: res.pagination?.total || res.total || 0,
                                        recordsFiltered: res.pagination?.total || res.total || 0,
                                        data: res.appointments || []
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('DataTable AJAX Error:', error);
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: 0,
                                        recordsFiltered: 0,
                                        data: []
                                    });
                                    Swal.fire('Error', 'Failed to load appointments.', 'error');
                                }
                            });
                        },
                        columns: [
                            {
                                data: 'patient',
                                render: function(data, type, row) {
                                    let patientName = row.patient?.fullname || 'N/A';
                                    let defaultImage = '/images/default-avatar.png';
                                    let profileImage = row.patient?.profile || defaultImage;
                                    return `
                                        <img width="50" height="50" src="${profileImage}" class="rounded-circle" alt="Patient Avatar">
                                        <span>${ucfirst(patientName)}</span>
                                    `;
                                },
                                className: "patient-cell view-appointment",
                                createdCell: function(td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                orderable: false
                            },
                            {
                                data: 'doctor',
                                render: function(data, type, row) {
                                    return row.doctor?.fullname || 'N/A';
                                },
                                className: "view-appointment",
                                createdCell: function(td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                orderable: false
                            },
                            {
                                data: 'treatment',
                                render: function(data, type, row) {
                                    return row.treatment?.name || 'N/A';
                                },
                                className: "d-none d-md-table-cell view-appointment",
                                createdCell: function(td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                orderable: false
                            },
                            {
                                data: 'date',
                                render: function(data, type, row) {
                                    return formatDate(row);
                                },
                                className: "d-none d-md-table-cell view-appointment",
                                createdCell: function(td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                }
                            },
                            @if($currentProjectTypeId !== 3)
                            {
                                data: 'status',
                                render: function(data, type, row) {
                                    return getStatusBadge(row.status);
                                },
                                className: "d-none d-md-table-cell view-appointment",
                                createdCell: function(td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                }
                            },
                            @endif
                            @if ($razorpayEnabled)
                            {
                                data: 'id',
                                render: function(data, type, row) {
                                    return buildPaymentHtml(row);
                                },
                                className: "d-none d-md-table-cell",
                                orderable: false
                            },
                            @endif
                            {
                                data: 'id',
                                render: function(data, type, row) {
                                    return buildActionsHtml(row);
                                },
                                className: "d-none d-md-table-cell",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function() {
                                    return `
                                        <button class="btn btn-link expand-btn">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                className: "d-table-cell d-md-none text-center",
                                orderable: false
                            }
                        ],
                        order: [[3, 'desc']],
                        pageLength: 10,
                        initComplete: function() {
                            const statusFromUrl = getQueryParam('status');
                            if (statusFromUrl && statusFromUrl !== 'all') {
                                $('#statusFilter').val(statusFromUrl);
                                $('#statusFilter').trigger('change');
                            }
                        }
                    });
                }, 50);
            }

            initializeAppointmentsTable();

            // Status filter
            $('#statusFilter').on('change', function() {
                var selectedStatus = $(this).val();
                if (!appointmentTable) return;
                if (selectedStatus === "all") {
                    appointmentTable.search('').draw();
                } else {
                    appointmentTable.search(selectedStatus).draw();
                }
            });

            // Get query param from URL
            function getQueryParam(param) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }
            // Status param is applied in DataTable initComplete

            // Download appointment PDF
            $(document).on('click', '.download-appointment', function() {
                const appointmentId = $(this).data('id');
                Swal.fire({
                    title: 'Downloading...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: `/api/appointments/${appointmentId}/download`,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/pdf'
                    },
                    success: function(response, status, xhr) {
                        Swal.close();
                        let filename = `Appointment_${appointmentId}.pdf`;
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            filename = disposition.split('filename=')[1].replace(/"/g, '');
                        }
                        const blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;
                        link.click();
                    },
                    error: function(xhr) {
                        Swal.close();
                        let message = "Failed to download PDF.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    }
                });
            });

            $(document).on('click', '.expand-btn', function() {
                const btn = $(this);
                const icon = btn.find('i');
                const tr = btn.closest('tr');
                const existingRow = tr.next('.details-row');

                // Toggle visibility if row already exists
                if (existingRow.length) {
                    existingRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let rowData = null;
                if (appointmentTable) {
                    const row = appointmentTable.row(tr);
                    rowData = row.data();
                }

                const treatment = rowData?.treatment?.name || 'N/A';
                const date = rowData ? formatDate(rowData) : 'N/A';
                const status = rowData ? getStatusBadge(rowData.status) : 'N/A';
                const paymentHtml = rowData ? buildPaymentHtml(rowData) : '';
                const actionHtml = rowData ? buildActionsHtml(rowData) : '';

                // Create and insert the details row
                const detailsRow = $(`
                                        <tr class="details-row">
                                            <td colspan="3">
                                                <div class="details-content">
                                                    <div><strong>Treatment:</strong> ${treatment}</div>
                                                    <div><strong>Date:</strong> ${date}</div>
                                                    @if($currentProjectTypeId !== 3)
                                                    <div><strong>Status:</strong> ${status}</div>
                                                    @endif
                                                    <div class="mt-2"><strong>Payment:</strong><br>${paymentHtml}</div>
                                                    <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    `);

                tr.after(detailsRow);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });

            // link
            $(document).on('click', '.generate-appointment-link-btn', function() {

                let appointmentId = $(this).data('id');
                let status = $(this).data('status');

                // ✅ Already paid check
                if (status === 'paid') {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Pending Amount',
                        text: 'This appointment payment is already completed.'
                    });
                    return;
                }

                // 🔹 Ask amount
                Swal.fire({
                    title: 'Generate Payment Link',
                    html: `
                                <label style="float:left;margin-left:50px;">Enter Amount (₹)</label>
                                <input type="number"
                                    id="paymentAmount"
                                    class="swal2-input"
                                    placeholder="Enter amount"
                                    min="1"
                                    style="width:80%;margin:10px auto;">
                            `,
                    showCancelButton: true,
                    confirmButtonText: '<span style="color:black">Generate Link</span>',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#cfece0',
                    cancelButtonColor: '#f89884',
                    customClass: {
                        confirmButton: 'swal-confirm-btn'
                    },
                    preConfirm: () => {
                        const amount = document.getElementById('paymentAmount').value;
                        if (!amount || amount <= 0) {
                            Swal.showValidationMessage('Please enter a valid amount');
                        }
                        return amount;
                    }
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    // 🔹 Backend call
                    $.ajax({
                        url: `/api/razorpay/appointment/generate-payment-link`,
                        type: 'POST',
                        data: {
                            appointment_id: appointmentId,
                            amount: result.value,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {

                            // 📋 Copy link
                            navigator.clipboard.writeText(res.payment_link);

                            // ✅ SUCCESS POPUP (same as report page)
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Link Generated',
                                html: `
                                        <p><strong>Patient Name:</strong> ${res.patient_name}</p>
                                        <p><strong>Contact No:</strong> ${res.patient_phone}</p>
                                        <p><strong>Amount:</strong> ₹${res.amount}</p>

                                        <input type="text"
                                            value="${res.payment_link}"
                                            readonly
                                            style="
                                                width:100%;
                                                padding:12px;
                                                margin-top:15px;
                                                border-radius:25px;
                                                border:1.5px solid #ff8c6b;
                                                font-size:18px;
                                            "
                                        />

                                        <div style="text-align:center;font-size:13px;margin-top:6px;">
                                            Link copied to clipboard ✔
                                        </div>
                                    `,
                                showConfirmButton: false, // Hide OK button
                                showCancelButton: true, // Show only cancel button
                                cancelButtonText: 'Close',
                                cancelButtonColor: '#f89884',
                                customClass: {
                                    cancelButton: 'custom-cancel-btn' // add custom class
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason
                                    .cancel) {
                                    location
                                        .reload(); // Reload page when Cancel clicked
                                }

                            });
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message ||
                                'Failed to generate payment link';

                            Swal.fire({
                                icon: xhr.status === 403 ? 'warning' : 'error',
                                title: xhr.status === 403 ?
                                    'Razorpay Disabled' : 'Error',
                                text: message,
                                showConfirmButton: false, // hide OK button
                                showCancelButton: true, // show Cancel button instead
                                cancelButtonText: 'Close',
                                cancelButtonColor: '#f89884',
                                customClass: {
                                    cancelButton: 'swal-cancel-btn' // apply your custom style
                                }
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason
                                    .cancel) {
                                    location
                                        .reload(); // Reload page when Cancel clicked
                                }

                            });
                        }

                    });
                });
            });


            // Follow-up Appointment
            $(document).on('click', '.followup-appointment', function() {
                const appointmentId = $(this).data('id');
                const userId = $(this).data('user_id');
                $.ajax({
                    url: `/api/appointments/${appointmentId}`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        const data = response.data;
                        // Set dynamic data for the follow-up modal
                        $('#followupModal').data('appointment_id', appointmentId);
                        $('#followupModal').data('patient_id', data.patient_id);
                        $('#followupModal').data('treatment_id', data.treatment_id);
                        // Show the follow-up modal
                        $('#followupModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to fetch appointment details.', 'error');
                    }
                });
            });

            // Reschedule Appointment
            $(document).on('click', '.reschedule-appointment', function() {
                const appointmentId = $(this).data('id');
                $.ajax({
                    url: `/api/appointments/${appointmentId}`,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        const data = response.data;
                        // Set dynamic data for the reschedule modal
                        $('#rescheduleModal').data('appointment_id', appointmentId);
                        // Show the reschedule modal
                        $('#rescheduleModal').modal('show');
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to fetch appointment details.', 'error');
                    }
                });
            });

            // Complete appointment
            $(document).on('click', '.complete-appointment', function() {
                const appointmentId = $(this).data('id');
                Swal.fire({
                    title: 'Complete Appointment?',
                    text: "Do you want to mark this appointment as completed?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#cfece0',
                    cancelButtonColor: '#f89884',
                    confirmButtonText: 'Yes, complete it',
                    cancelButtonText: 'No',
                    customClass: {
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Create Invoice?',
                            text: "Do you want to generate an invoice for this appointment?",
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#cfece0',
                            confirmButtonText: 'Yes, create invoice',
                            cancelButtonColor: '#f89884',
                            cancelButtonText: 'No',
                            customClass: {
                                confirmButton: 'swal-confirm-btn',
                                cancelButton: 'swal-cancel-btn'
                            }
                        }).then((res) => {
                            if (res.isConfirmed) {
                                $.ajax({
                                    url: "/api/appointments/" + appointmentId,
                                    method: 'GET',
                                    data: {
                                        branch_id: branchId
                                    },
                                    headers: {
                                        'Authorization': 'Bearer ' + token,
                                        'Accept': 'application/json'
                                    },
                                    success: function(response) {
                                        const data = response.data;
                                        const modal = new bootstrap.Modal(
                                            document.getElementById(
                                                'invoiceModal'));
                                        modal.show();
                                        $('#appointment_id').val(data.id);
                                        $('#patient_name').val(data.patient
                                            .fullname);
                                        $('#doctor_name').val(data.doctor
                                            .fullname);
                                        $('#treatment_name').val(data.treatment
                                            .name);
                                        $('#price').val(data.price || '');
                                        $('#invoice_date').val(new Date()
                                            .toISOString().split('T')[0]);
                                        $('#instruction').val('');
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Failed to fetch appointment details.',
                                            'error');
                                    }
                                });
                            } else {
                                $.ajax({
                                    url: '/api/appointments/complete',
                                    method: 'POST',
                                    headers: {
                                        'Authorization': 'Bearer ' + token,
                                        'Accept': 'application/json'
                                    },
                                    data: {
                                        id: appointmentId
                                    },
                                    success: function(response) {
                                        Swal.fire('Completed!',
                                            'Appointment marked as completed.',
                                            'success').then(() => location
                                            .reload());
                                    },
                                    error: function() {
                                        Swal.fire('Error',
                                            'Error completing appointment.',
                                            'error');
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Follow-up Appointment
            $(document).on('click', '.followup-appointment', function() {
                const appointmentId = $(this).data('id');
                const userId = $(this).data('user_id');
                // Show the follow-up modal for this appointment
                $(`#followupModal_${appointmentId}`).modal('show');
            });

            // Reschedule Appointment
            $(document).on('click', '.reschedule-appointment', function() {
                const appointmentId = $(this).data('id');
                // Show the reschedule modal for this appointment
                $(`#rescheduleModal_${appointmentId}`).modal('show');
            });


            // Handle invoice form submit
            $('#invoiceForm').submit(function(e) {
                e.preventDefault();

                let branchId = getSelectedBranchId();

                // ✅ Set hidden field value
                let formData = $(this).serializeArray();
                if (branchId) {
                    formData.push({
                        name: 'branch_id',
                        value: branchId
                    });
                }
                $.ajax({
                    url: '/api/invoices/generate-appointment-invoice',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            Swal.fire(
                                'Invoice Generated!',
                                'Invoice has been generated successfully.',
                                'success'
                            ).then(() => {
                                window.open(response.url, '_blank');
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', 'Failed to generate invoice.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errMsg = 'Something went wrong.';
                        if (xhr.responseJSON?.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errMsg, 'error');
                    }
                });

                // Close modal
                const modalEl = document.getElementById('invoiceModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            });


            // View, Edit, Delete handlers
            $(document).on('click', '.view-appointment', function() {
                var appointmentId = $(this).data('id');
                window.location.href = '/appointment/show/' + appointmentId;
            });
            $(document).on('click', '.edit-appointment', function() {
                var appointmentId = $(this).data('id');
                window.location.href = '/appointment/edit/' + appointmentId;
            });
            $(document).on('click', '.delete-appointment', function() {
                var appointmentId = $(this).data('id');
                if (!appointmentId) {
                    Swal.fire('Error', 'Appointment ID not found!', 'error');
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
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
                            url: '/api/appointments/' + appointmentId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'The appointment has been deleted.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                console.log(xhr.responseText);
                                Swal.fire('Error', 'Failed to delete the appointment.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Sidebar toggle
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('toggle_btn');
                const sidebar = document.querySelector('.sidebar');
                if (toggleBtn && sidebar) {
                    toggleBtn.addEventListener('click', function() {
                        sidebar.classList.toggle('mini-sidebar');
                    });
                }
            });
        });
    </script>
@endsection
