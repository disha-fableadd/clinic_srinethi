@extends('layout.app')
@section('content')
<style>
    .expand-invoice-btn {
        border: none;
        background: #f89884;
        color: white;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .table td,
    .table th {

        vertical-align: middle !important;

    }

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
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

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
            text-align: left !important;
        }

        .invoice-icons {
            display: flex !important;

        }

        .invoice-icons .delete-invoice {
            margin-left: 2px !important;
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

    button.btn.btn-link.expand-invoice-btn {
        background-color: #f89884 !important;
        border-radius: 14px !important;
        color: white !important;
    }

    a.btn.btn-sm.btn-light.mt-1 {
        background-color: #f5b6a5 !important;
    }

    button.delete-invoice.m-r-5.icon2 {
        border: none !important;
    }

    /* Revenue Statistics in Header */
    .invoice-card-header {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        padding: 12px 18px !important;
    }

    .invoice-card-header .card-title {
        margin: 0 !important;
        margin-right: auto !important;
    }

    .invoice-header-actions {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .invoice-revenue-stats {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rev-stat-badge {
        background: #ffffff;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        line-height: 1.2;
        white-space: nowrap;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .rev-stat-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    }

    .rev-stat-badge.total-rev {
        border-left: 3.5px solid #2563eb;
    }

    .rev-stat-badge.paid-rev {
        border-left: 3.5px solid #16a34a;
    }

    .rev-stat-badge.pending-rev {
        border-left: 3.5px solid #ea580c;
    }

    .rev-icon-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .rev-stat-badge .rev-label {
        font-weight: 600;
        color: #4b5563;
    }

    .rev-stat-badge .rev-value {
        font-weight: 700;
        color: #111827;
        font-size: 13.5px;
    }

    /* Tablet and Mobile adjustments */
    @media screen and (max-width: 991px) {
        .invoice-card-header {
            justify-content: space-between !important;
            padding: 12px 14px !important;
        }

        .invoice-card-header .card-title {
            order: 1 !important;
            margin-right: auto !important;
            font-size: 17px !important;
        }

        .invoice-header-actions {
            order: 2 !important;
            margin-left: auto !important;
        }

        .invoice-revenue-stats {
            order: 3 !important;
            width: 100% !important;
            margin-top: 8px !important;
            margin-right: 0 !important;
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
        }

        .rev-stat-badge {
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            padding: 8px 6px !important;
            border-radius: 12px !important;
            border-left: none !important;
            border-top: 3.5px solid transparent !important;
            min-width: 0 !important;
        }

        .rev-stat-badge.total-rev {
            border-top-color: #2563eb !important;
        }

        .rev-stat-badge.paid-rev {
            border-top-color: #16a34a !important;
        }

        .rev-stat-badge.pending-rev {
            border-top-color: #ea580c !important;
        }

        .rev-icon-label {
            margin-bottom: 2px;
        }

        .rev-stat-badge .rev-label {
            font-size: 11px !important;
            line-height: 1.1 !important;
        }

        .rev-stat-badge .rev-value {
            font-size: 13px !important;
            line-height: 1.2 !important;
        }
    }

    /* Small Mobile Phones (e.g. <= 480px) */
    @media screen and (max-width: 480px) {
        .invoice-revenue-stats {
            gap: 5px !important;
        }

        .rev-stat-badge {
            padding: 6px 3px !important;
            border-radius: 10px !important;
        }

        .rev-stat-badge .rev-label {
            font-size: 10px !important;
        }

        .rev-stat-badge .rev-value {
            font-size: 11.5px !important;
        }

        .invoice-header-actions .btn-hdr {
            padding: 5px 9px !important;
            font-size: 12px !important;
        }
    }
</style>

@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
@endphp
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header invoice-card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-file-invoice-dollar px-2" style="font-size:20px"></i> All Invoice
                            </h3>
                            <div class="invoice-revenue-stats">
                                <div class="rev-stat-badge total-rev" title="Total Revenue">
                                    <div class="rev-icon-label">
                                        <i class="fas fa-coins text-primary"></i>
                                        <span class="rev-label">
                                            <span class="d-none d-sm-inline">Total Revenue:</span>
                                            <span class="d-sm-none">Total:</span>
                                        </span>
                                    </div>
                                    <span class="rev-value" id="statTotalRevenue">₹0.00</span>
                                </div>
                                <div class="rev-stat-badge paid-rev" title="Paid Revenue">
                                    <div class="rev-icon-label">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span class="rev-label">
                                            <span class="d-none d-sm-inline">Paid Revenue:</span>
                                            <span class="d-sm-none">Paid:</span>
                                        </span>
                                    </div>
                                    <span class="rev-value" id="statPaidRevenue">₹0.00</span>
                                </div>
                                <div class="rev-stat-badge pending-rev" title="Pending Revenue">
                                    <div class="rev-icon-label">
                                        <i class="fas fa-clock text-warning"></i>
                                        <span class="rev-label">
                                            <span class="d-none d-sm-inline">Pending Revenue:</span>
                                            <span class="d-sm-none">Pending:</span>
                                        </span>
                                    </div>
                                    <span class="rev-value" id="statPendingRevenue">₹0.00</span>
                                </div>
                            </div>
                            <div class="invoice-header-actions">
                                <button class="btn btn-rounded btn-hdr" id="exportButton">
                                    <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                                </button>
                                @if (app('hasPermission')(18, 'create'))
                                    <a href="{{ route('invoice.create') }}" class="btn btn-rounded btn-hdr">
                                        <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body mt-3">
                            <div class="table-responsive">
                                <table class="table " id="invoiceTable">
                                    <thead>
                                        <tr>
                                            <th class="d-none">ID</th>
                                            <th>Patient</th>
                                            <th>Total</th>
                                            <th class="d-none d-md-table-cell">Payment Status</th>
                                            <th class="d-none d-md-table-cell">Created At</th>
                                            @if($razorpayEnabled)
                                                <th class="d-none d-md-table-cell">Payment Link</th>

                                            @endif
                                            <th class="d-none d-md-table-cell">Actions</th>
                                            <th class="d-table-cell d-md-none text-center">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>



                                </table>
                            </div>
                            <div id="noDataMsg" class="alert alert-info text-center d-none">No invoice details available.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>


    <script>
        // $(document).on('click', '#exportButton', function() {
        //     // Get branch ID from localStorage
        //     let branchId = localStorage.getItem('selectedBranchId');
        //     // Redirect to export route with branch_id as query parameter
        //     window.location.href = "{{ route('invoice.export') }}" + "?branch_id=" + branchId;
        // });


        $(document).on('click', '#exportButton', function () {
            let branchId = localStorage.getItem('selectedBranchId');
            window.location.href = "{{ route('invoice.export') }}" + "?branch_id=" + branchId;
        });

        window.addEventListener('DOMContentLoaded', function () {

            function eventFired(type) {
                let n = document.querySelector('#demo_info');
                if (n) {
                    n.scrollTop = n.scrollHeight;
                }
            }

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
            $(document).on('click', '.expand-invoice-btn', function () {

                const btn = $(this);
                const icon = btn.find('i');
                const tr = btn.closest('tr');
                const invoiceId = btn.data('id');
                const existingRow = tr.next('.details-row');

                // Toggle existing
                if (existingRow.length) {
                    existingRow.slideToggle(200);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                // Get data from row (filter out hidden td:eq(0) if present)
                const visibleTds = tr.find('td:visible');
                const patientInfo = (visibleTds.length > 0 ? visibleTds.eq(0).html() : tr.find('td').eq(1).html());
                const total = (visibleTds.length > 1 ? visibleTds.eq(1).text() : tr.find('td').eq(2).text());
                const status = (visibleTds.length > 2 ? visibleTds.eq(2).text() : tr.find('td').eq(3).text());
                const date = (visibleTds.length > 3 ? visibleTds.eq(3).text() : tr.find('td').eq(4).text());

                const linkBtn = `
                                                    <button class="btn btn-sm btn-outline-success generate-invoice-link-btn mt-2"
                                                        data-id="${invoiceId}"
                                                        data-amount="${total.replace('₹', '')}"
                                                        data-status="${status}">
                                                        <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span></button>
                                                `;

                const actions = `
                                                    <a href="/invoice/pdf/${invoiceId}?stream=1" target="_blank" class="btn btn-sm btn-light mt-1" title="View Invoice">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="/invoice/edit/${invoiceId}" class="btn btn-sm btn-light mt-1 edit-invoice" title="Edit Invoice">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="/invoice/pdf/${invoiceId}" class="btn btn-sm btn-light mt-1" title="Download Invoice">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-invoice mt-1"
                                                        data-id="${invoiceId}" title="Delete Invoice">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                `;

                const detailsRow = $(`
                                                        <tr class="details-row">
                                                            <td colspan="7">
                                                                <div class="details-content">
                                                                    <div><strong>Patient:</strong> ${patientInfo}</div>
                                                                    <div><strong>Total:</strong> ${total}</div>
                                                                    <div><strong>Status:</strong> ${status}</div>
                                                                    <div><strong>Date:</strong> ${date}</div>

                                                                    <div class="mt-2">
                                                                        <strong>Payment Link:</strong><br>
                                                                        ${linkBtn}
                                                                    </div>

                                                                    <div class="mt-2">
                                                                        <strong>Actions:</strong><br>
                                                                        ${actions}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    `);

                tr.after(detailsRow);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });
            $(window).on('resize', function () {
                if (window.innerWidth >= 768) { // Bootstrap md breakpoint
                    $('.details-row').remove(); // remove expanded rows
                    $('.expand-invoice-btn i')
                        .removeClass('fa-chevron-up')
                        .addClass('fa-chevron-down'); // reset icon
                }
            });


            function fetchinvoice() {
                // Get branch ID from localStorage
                let branchId = localStorage.getItem('selectedBranchId');

                $.ajax({
                    url: "{{ route('invoice.index') }}",
                    type: "GET",
                    dataType: "json",
                    data: {
                        branch_id: branchId // send selected branch to backend
                    },
                    success: function (response) {
                        if (!response?.status || !Array.isArray(response.data)) {
                            $('#noDataMsg').removeClass('d-none').text(response.message ||
                                'No invoice data found.');
                            $('#statTotalRevenue').text('₹0.00');
                            $('#statPaidRevenue').text('₹0.00');
                            $('#statPendingRevenue').text('₹0.00');
                            return;
                        }

                        let data = response.data;

                        // Update revenue statistics
                        let totalRev = 0;
                        let paidRev = 0;
                        let pendingRev = 0;

                        if (response.summary) {
                            totalRev = parseFloat(response.summary.total_revenue) || 0;
                            paidRev = parseFloat(response.summary.paid_revenue) || 0;
                            pendingRev = parseFloat(response.summary.pending_revenue) || 0;
                        } else {
                            data.forEach(invoice => {
                                const amt = parseFloat(invoice.grand_total) || 0;
                                totalRev += amt;
                                const st = (invoice.payment_status || '').toLowerCase();
                                if (st === 'paid') {
                                    paidRev += amt;
                                } else {
                                    pendingRev += amt;
                                }
                            });
                        }

                        $('#statTotalRevenue').text('₹' + totalRev.toFixed(2));
                        $('#statPaidRevenue').text('₹' + paidRev.toFixed(2));
                        $('#statPendingRevenue').text('₹' + pendingRev.toFixed(2));

                        // Destroy existing DataTable instance
                        if ($.fn.DataTable.isDataTable("#invoiceTable")) {
                            $('#invoiceTable').DataTable().destroy();
                        }

                        // Clear old rows
                        let tableBody = $('#invoiceTable tbody');
                        tableBody.empty();

                        data.forEach(invoice => {
                            const patientName = invoice.patient?.fullname ? ucfirst(invoice.patient.fullname) : 'N/A';
                            const invoiceType = invoice.type || '';

                            // Extract item/treatment name(s) if available
                            let itemNames = [];
                            if (invoice.types_details) {
                                let details = invoice.types_details;
                                if (typeof details === 'string') {
                                    try {
                                        details = JSON.parse(details);
                                    } catch (e) {
                                        details = [];
                                    }
                                }
                                if (Array.isArray(details)) {
                                    details.forEach(item => {
                                        if (item && item.name) {
                                            itemNames.push(item.name);
                                        }
                                    });
                                }
                            }

                            let typeDetailsHtml = `<div><strong style="color: #2c3e50;">Type:</strong> <span style="font-weight: 600; color: #34495e;">${ucfirst(invoiceType)}</span></div>`;
                            if (invoiceType.toLowerCase() !== 'appointment' && itemNames.length > 0) {
                                typeDetailsHtml += `<div><strong style="color: #2c3e50;">Name:</strong> <span style="font-weight: 600; color: #34495e;">${itemNames.join(', ')}</span></div>`;
                            }

                            let row = `
                                            <tr data-invoice-id="${invoice.id}">
                                                <td class="d-none">${invoice.id}</td>
                                                <td>
                                                    <strong style="font-size: 14px; color: #1a202c;">${patientName}</strong>
                                                    <div style="font-size: 12px; line-height: 1.5; margin-top: 3px;">
                                                        ${typeDetailsHtml}
                                                    </div>
                                                </td>
                                                <td>₹${parseFloat(invoice.grand_total).toFixed(2)}</td>

                                                <td class="d-none d-md-table-cell">${ucfirst(invoice.payment_status)}</td>
                                                <td class="d-none d-md-table-cell">${new Date(invoice.created_at).toLocaleDateString('en-GB')}</td>
                                                @if($razorpayEnabled)
                                                    <td class="d-none d-md-table-cell">
                                                        <button class="btn btn-sm btn-outline-success generate-invoice-link-btn"
                                                            data-id="${invoice.id}"
                                                            data-amount="${invoice.grand_total}"
                                                            data-status="${invoice.payment_status}">
                                                            <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                                                        </button>
                                                    </td>

                                                @endif
                                                <td class="d-none d-md-table-cell">
                                                    <div class="invoice-icons icon">
                                                        <a href="/invoice/pdf/${invoice.id}?stream=1" target="_blank" class="m-r-5 icon3" title="View Invoice">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="/invoice/edit/${invoice.id}" class="m-r-5 icon1 edit-invoice" title="Edit Invoice">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a href="/invoice/pdf/${invoice.id}" class="m-r-5 icon4" title="Download Invoice">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button class="delete-invoice m-r-5 icon2" data-id="${invoice.id}" title="Delete Invoice">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                                <!-- Mobile expand button -->
                                                <td class="d-table-cell d-md-none text-center">
                                                    <button class="btn btn-link expand-invoice-btn" data-id="${invoice.id}">
                                                        <i class="fa fa-chevron-down"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            `;

                            tableBody.append(row);
                        });


                        // Initialize DataTable with descending order by ID
                        $('#invoiceTable').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            order: [[0, 'desc']], // Descending order by ID
                            responsive: true,
                            autoWidth: false,
                            columnDefs: [
                                { targets: [0], visible: false, searchable: false }
                            ]
                        })
                            .on('order.dt', () => eventFired('Order'))
                            .on('search.dt', () => eventFired('Search'))
                            .on('page.dt', () => eventFired('Page'));
                    },
                    error: function () {
                        $('#noDataMsg').removeClass('d-none').text('Error fetching invoice data.');
                    }
                });
            }

            // Call fetchinvoice once DOM is ready
            fetchinvoice();
        });


        // Generate invoice payment link on button click
        $(document).on('click', '.generate-invoice-link-btn', function () {
            let invoiceId = $(this).data('id');
            let amount = $(this).data('amount');
            let status = $(this).data('status'); // e.g., 'paid' or 'pending'

            // Check if invoice is already paid
            if (status?.toLowerCase() === 'paid' || !amount || amount <= 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No pending amount',
                    text: 'This invoice has no pending amount for payment.',
                    showConfirmButton: false, // Hide default OK button
                    showCancelButton: true, // Show Cancel button
                    cancelButtonText: 'Close', // Text for cancel button
                    cancelButtonColor: '#f89884', // Button color
                    customClass: {
                        cancelButton: 'custom-cancel-btn' // Your custom CSS class
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        location.reload(); // Reload page on Cancel click
                    }
                });
                return;
            }

            // Generate payment link if pending amount exists
            $.ajax({
                url: `/api/razorpay/invoice/generate-payment-link`,
                type: 'POST',
                data: {
                    invoice_id: invoiceId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    // Copy link to clipboard
                    navigator.clipboard.writeText(res.payment_link);

                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Link Generated',
                        html: `
                                    <p><strong>Patient Name:</strong> ${res.customer_name}</p>
                                    <p><strong>Patient No:</strong> ${res.customer_phone}</p>
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
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        cancelButtonColor: '#f89884',
                        customClass: {
                            cancelButton: 'custom-cancel-btn'
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            location.reload();
                        }
                    });
                },
                error: function (xhr) {
                    let message = xhr.responseJSON?.message || 'Failed to generate payment link';

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
                            location.reload();
                        }
                    });
                }

            });
        });





        // Delete invoice
        $(document).on('click', '.delete-invoice', function () {
            var invoiceId = $(this).data('id');

            if (!invoiceId) {
                Swal.fire('Error', 'Invoice ID not found!', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#cfece0', // background for Yes
                cancelButtonColor: '#f89884', // background for Cancel
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/invoice/' + invoiceId,
                        type: 'DELETE',
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Refresh table after deletion
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to delete invoice.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
