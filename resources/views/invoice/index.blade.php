@extends('layout.app')


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
</style>

@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
@endphp

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-file-invoice-dollar px-2" style="font-size:20px"></i> All Invoice
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(18, 'create'))
                                <a href="{{ route('invoice.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body mt-3">
                            <div class="table-responsive">
                                <table class="table " id="invoiceTable">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
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

                // Get data from row
                const type = tr.find('td').eq(0).text();
                const total = tr.find('td').eq(1).text();
                const status = tr.find('td').eq(2).text();
                const date = tr.find('td').eq(3).text();

                const linkBtn = `
                                                    <button class="btn btn-sm btn-outline-success generate-invoice-link-btn mt-2"
                                                        data-id="${invoiceId}"
                                                        data-amount="${total.replace('₹', '')}"
                                                        data-status="${status}">
                                                        <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span></button>
                                                `;

                const actions = `
                                                    <a href="/invoice/pdf/${invoiceId}" class="btn btn-sm btn-light mt-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-invoice mt-1"
                                                        data-id="${invoiceId}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                `;

                const detailsRow = $(`
                                                        <tr class="details-row">
                                                            <td colspan="7">
                                                                <div class="details-content">
                                                                    <div><strong>Type:</strong> ${type}</div>
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
                            return;
                        }

                        let data = response.data;

                        // Destroy existing DataTable instance
                        if ($.fn.DataTable.isDataTable("#invoiceTable")) {
                            $('#invoiceTable').DataTable().destroy();
                        }

                        // Clear old rows
                        let tableBody = $('#invoiceTable tbody');
                        tableBody.empty();

                        data.forEach(invoice => {
                            let row = `
                                            <tr data-invoice-id="${invoice.id}">
                                                <td>${ucfirst(invoice.type)}</td>
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
                                                        <a href="/invoice/pdf/${invoice.id}" class="m-r-5 icon1">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button class="delete-invoice m-r-5 icon2" data-id="${invoice.id}">
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


                        // Initialize DataTable
                        $('#invoiceTable').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            responsive: true,
                            autoWidth: false
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