@extends('layout.app')

<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    colgroup {
        display: none;
    }

    .swal-cancel-btn {
        color: white !important;
        border-radius: 23px !important;
    }

    .card-header {
        background-color: #f89884 !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
    }

    .custom-cancel-btn {
        border-radius: 23px !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
        }

        .report-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding-top: 15px !important;
        }

        .report-icon .download {
            margin-left: 5px !important;
        }
    }

    /* 🔹 Added responsive details styles */
    .details-row {
        background-color: #f8f9fa;
    }

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }

    button.expand-btn {
        background: #f89884;
        color: white;
        padding: 6px 8px;
        border-radius: 8px;
        border: none;
    }

    /* Hide details column on desktop */
    @media (min-width: 768px) {
        .d-table-cell.d-md-none {
            display: none !important;
        }

        .details-row {
            display: none !important;
        }
    }

    /* Hide desktop columns on mobile */
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

    .swal-confirm-btn {
        color: #000 !important;
        /* BLACK text */
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
                                <i class="fa fa-procedures px-2" style="font-size: 20px;"></i>All Report
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(13, 'create'))
                                <a href="{{ route('report.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="example" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Patient</th>
                                            <th class="d-none d-md-table-cell">Report Type</th>
                                            <th class="d-none d-md-table-cell">Date</th>
                                            <th class="d-none d-md-table-cell">File</th>
                                            @if($razorpayEnabled)
                                                <th class="d-none d-md-table-cell">Payment Link</th>
                                            @endif
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="reportbody"></tbody>
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

    <script>
        let branchId = localStorage.getItem('selectedBranchId');

        $(document).on('click', '#exportButton', function () {
            window.location.href = "{{ route('report.export') }}";
        });

        document.addEventListener('DOMContentLoaded', function () {

            function ucfirst(str) {
                if (!str || typeof str !== 'string') return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            let reportTable;

            fetchReports();

            function fetchReports() {
                if ($.fn.DataTable.isDataTable("#example")) {
                    try {
                        let existingTable = $('#example').DataTable();
                        existingTable.destroy();
                        $('#example').removeClass('dataTable');
                        $('#example tbody').empty();
                        $.removeData($('#example')[0], 'DataTable');
                        $.removeData($('#example')[0], 'DataTables_DataTable');
                        $('#example').off();
                    } catch (e) {
                        $('#example tbody').empty();
                        $.removeData($('#example')[0]);
                    }
                }

                if ($('#example tbody').length === 0) {
                    $('#example').append('<tbody class="reportbody"></tbody>');
                }

                setTimeout(function () {
                    reportTable = $('#example').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        ajax: function (data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const perPage = data.length;
                            $.ajax({
                                url: "{{ url('/api/medical-reports') }}",
                                type: "GET",
                                dataType: "json",
                                data: {
                                    branch_id: branchId,
                                    user_id: (typeof userId !== 'undefined' && userId) ? userId : null,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function (response) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || 0,
                                        data: response.data || []
                                    });
                                },
                                error: function (xhr) {
                                    console.error("Error fetching reports:", xhr.responseText);
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
                                data: "patient",
                                render: function (data, type, row) {
                                    let name = data && data.fullname ? ucfirst(data.fullname) : 'N/A';
                                    return `<span class="view-report" data-id="${row.id}" style="cursor:pointer">${name}</span>`;
                                }
                            },
                            {
                                data: "report_type",
                                render: function (data, type, row) {
                                    return `<span class="view-report" data-id="${row.id}">${ucfirst(data)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "date",
                                render: function (data, type, row) {
                                    return `<span class="view-report" data-id="${row.id}">${ucfirst(data)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "file_path",
                                render: function (data) {
                                    if (!data) return '<span class="text-muted">N/A</span>';
                                    return `
                                        <a href="${data}" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-file-alt"></i></a>
                                        <a href="${data}" download class="btn btn-primary btn-sm download"><i class="fas fa-download"></i></a>
                                    `;
                                },
                                className: "d-none d-md-table-cell text-center report-icon",
                                orderable: false
                            },
                            @if($razorpayEnabled)
                                {
                                    data: "id",
                                    render: function (data, type, row) {
                                        return `
                                            <button class="btn btn-sm btn-outline-success generate-report-link-btn"
                                                data-id="${row.id}"
                                                data-amount="${row.amount}"
                                                data-status="${row.payment_status}">
                                                <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                                            </button>
                                        `;
                                    },
                                    className: "d-none d-md-table-cell",
                                    orderable: false
                                },
                            @endif
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return `
                                        <div class="icon" style="cursor:pointer">
                                            @if (app('hasPermission')(13, 'view')) 
                                                <i class="fa fa-eye m-r-5 icon3 view-report" data-id="${row.id}" title="View"></i> 
                                            @endif
                                            @if (app('hasPermission')(13, 'update')) 
                                                <i class="fa fa-pencil m-r-5 icon1 edit-report" data-id="${row.id}" title="Edit"></i> 
                                            @endif
                                            @if (app('hasPermission')(13, 'delete')) 
                                                <i class="fa fa-trash-o m-r-5 icon2 delete-report" data-id="${row.id}" title="Delete"></i> 
                                            @endif
                                            @if (app('hasPermission')(13, 'view')) 
                                                <i class="fa fa-download m-r-5 icon4 download-report" title="Download PDF" data-id="${row.id}"></i>
                                            @endif
                                        </div>
                                    `;
                                },
                                className: "d-none d-md-table-cell text-center",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="expand-btn">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                className: "d-table-cell d-md-none text-center",
                                orderable: false
                            }
                        ],
                        order: [[2, 'desc']]
                    });
                }, 50);
            }

            $(document).off('click', '.expand-btn').on('click', '.expand-btn', function () {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!reportTable || !$.fn.DataTable.isDataTable('#example')) {
                    return;
                }

                let row = reportTable.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let reportType = data.report_type ? ucfirst(data.report_type) : 'N/A';
                let date = data.date ? ucfirst(data.date) : 'N/A';
                let file = data.file_path ? `
                            <a href="${data.file_path}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-alt"></i>
                            </a>
                            <a href="${data.file_path}" download class="btn btn-primary btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                        ` : '<span class="text-muted">N/A</span>';

                let generateBtn = '';
                @if($razorpayEnabled)
                    generateBtn = `
                        <button class="btn btn-sm btn-outline-success generate-report-link-btn"
                            data-id="${data.id}"
                            data-amount="${data.amount}"
                            data-status="${data.payment_status}">
                            <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                        </button>
                    `;
                @endif

                let actions = `
                    <div class="icon" style="cursor:pointer">
                        @if (app('hasPermission')(13, 'view')) 
                            <i class="fa fa-eye m-r-5 icon3 view-report" data-id="${data.id}" title="View"></i> 
                        @endif
                        @if (app('hasPermission')(13, 'update')) 
                            <i class="fa fa-pencil m-r-5 icon1 edit-report" data-id="${data.id}" title="Edit"></i> 
                        @endif
                        @if (app('hasPermission')(13, 'delete')) 
                            <i class="fa fa-trash-o m-r-5 icon2 delete-report" data-id="${data.id}" title="Delete"></i> 
                        @endif
                        @if (app('hasPermission')(13, 'view')) 
                            <i class="fa fa-download m-r-5 icon4 download-report" title="Download PDF" data-id="${data.id}"></i>
                        @endif
                    </div>
                `;

                let detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="{{ $razorpayEnabled ? 7 : 6 }}">
                            <div class="details-content">
                                <div><strong>Report Type:</strong> ${reportType}</div>
                                <div><strong>Date:</strong> ${date}</div>
                                <div class="mt-2"><strong>File:</strong><br/>${file}</div>
                                <div class="mt-2"><strong>Payment:</strong><br/>${generateBtn}</div>
                                <div class="mt-2"><strong>Actions:</strong><br/>${actions}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });
            $(document).on('click', '.generate-report-link-btn', function () {

                let reportId = $(this).data('id');
                let status = $(this).data('status');

                // ✅ Already paid
                if (status === 'paid') {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Pending Amount',
                        text: 'This report payment is already completed.'
                    });
                    return;
                }

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
                    // ✅ Button colors
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

                    let amount = result.value;

                    // ✅ Call backend AFTER amount is entered
                    $.ajax({
                        url: `/api/razorpay/report/generate-payment-link`,
                        type: 'POST',
                        data: {
                            report_id: reportId,
                            amount: amount,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {

                            navigator.clipboard.writeText(res.payment_link);

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
                                showConfirmButton: false, // ❌ removes OK button
                                showCancelButton: true, // ✅ only Cancel button

                                cancelButtonText: 'Close',
                                cancelButtonColor: '#f89884',
                                customClass: {
                                    cancelButton: 'custom-cancel-btn' // add custom class
                                }
                            }).then((result) => {
                                // 🔄 Reload page when Cancel is clicked
                                if (result.dismiss === Swal.DismissReason
                                    .cancel) {
                                    location.reload();
                                }
                            });
                        },
                        error: function (xhr) {
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
                                // 🔄 Reload page when Cancel is clicked
                                if (result.dismiss === Swal.DismissReason
                                    .cancel) {
                                    location.reload();
                                }
                            });
                        }

                    });
                });
            });



            // 🧩 Your existing event handlers remain unchanged
            $(document).on('click', '.download-report', function () {
                const reportId = $(this).data('id');
                $.ajax({
                    url: `/api/medical-reports/${reportId}/download`,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (response, status, xhr) {
                        Swal.close();
                        let filename = `MedicalReport_${reportId}.pdf`;
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
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to download PDF.'
                        });
                    }
                });
            });

            $(document).on("click", ".delete-report", function () {
                let reportId = $(this).data("id");
                if (!reportId) {
                    Swal.fire('Error', 'Report ID not found!', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
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
                            url: "{{ url('/api/medical-reports') }}/" + reportId,
                            type: "DELETE",
                            success: function () {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Report deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    fetchReports();
                                });
                            },
                            error: function (xhr) {
                                Swal.fire('Error',
                                    'An error occurred while deleting the report.',
                                    'error');
                            }
                        });
                    }
                });
            });

            $(document).on("click", ".view-report", function () {
                let reportId = $(this).data("id");
                window.location.href = '/report/show/' + reportId;
            });

            $(document).on("click", ".edit-report", function () {
                let reportId = $(this).data("id");
                window.location.href = '/report/edit/' + reportId;
            });
        });
    </script>
@endsection
