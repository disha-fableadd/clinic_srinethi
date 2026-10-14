@extends('layout.app')

<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    colgroup {
        display: none;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
        border-radius: 23px !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
    }

    .card-header {
        background-color: #f89884 !important;
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



    .swal-ok-btn:hover {
        background-color: #ff7a55 !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 19px !important;
            padding-left: 10px !important;
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

    .custom-cancel-btn {
        border-radius: 23px !important;
    }

    button.expand-btn {
        background: #f89884 !important;
        color: white !important;
        border: none !important;
        padding: 8px !important;
        border-radius: 10px !important;
    }

    i.fa.fa-eye.m-r-5.view-discharge,
    i.fa.fa-pencil.m-r-5.edit-discharge,
    i.fa.fa-trash-o.m-r-5.delete-discharge {
        background: #f89884 !important;
        color: white !important;
        border-radius: 10px !important;
        padding: 8px !important;
    }
</style>


@php
    use App\Models\Setting;
    $razorpayEnabled = Setting::getValue('razorpay_status') === 'on';
@endphp
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row">

            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white"><i class="fa fa-procedures px-2"></i>All
                                Discharge </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(10, 'create'))
                                <a href="{{ route('discharge.create') }}" class="btn btn-rounded btn-hdr"><i
                                        class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="dischargetbl" class="table  custom-table">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Patient</th>
                                            <th class="d-none d-md-table-cell">Discharge Date</th>
                                            <th class="d-none d-md-table-cell">Total Bill</th>
                                            <th class="d-none d-md-table-cell">Payment</th>
                                            @if ($razorpayEnabled)
                                                <th class="d-none d-md-table-cell">Payment Link</th>
                                            @endif

                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>

                                    <tbody class="discharge">


                                    </tbody>
                                </table>
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
        //     window.location.href = "{{ route('discharge.export') }}";
        // });

        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId'); // get from localStorage

            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }

            // Redirect to export route with branch_id param
            window.location.href = "{{ route('discharge.export') }}" + "?branch_id=" + branchId;
        });



        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar'); // Assuming the sidebar has this class

            toggleBtn.addEventListener('click', function() {
                if (sidebar) {
                    sidebar.classList.toggle('mini-sidebar'); // This toggles the class on the sidebar
                }
            });
        });


        $(document).ready(function() {
            fetchdischarge();
        });

        let dischargeTable;

        function ucfirst(str) {
            if (!str) return 'N/A';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // function fetchdischarge() {
        //     let branchId = localStorage.getItem('selectedBranchId');

        //     if ($.fn.DataTable.isDataTable("#dischargetbl")) {
        //         dischargeTable = $('#dischargetbl').DataTable();
        //         dischargeTable.ajax.reload(null, false);
        //         return;
        //     }

        //     dischargeTable = $('#dischargetbl').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         retrieve: true,
        //         ajax: function(data, callback) {
        //             const page = Math.floor(data.start / data.length) + 1;

        //             $.ajax({
        //                 url: "{{ url('/api/patient-discharge-details') }}",
        //                 type: "GET",
        //                 data: {
        //                     branch_id: branchId,
        //                     user_id: userId,
        //                     page: page,
        //                     per_page: data.length,
        //                     search: data.search?.value || ''
        //                 },
        //                 headers: {
        //                     "Authorization": "Bearer " + token
        //                 },
        //                 success: function(json) {
        //                     callback({
        //                         draw: data.draw,
        //                         recordsTotal: json.pagination?.total || 0,
        //                         recordsFiltered: json.pagination?.total || 0,
        //                         data: json.dischargeDetails || []
        //                     });
        //                 },
        //                 error: function(xhr, status, error) {
        //                     console.error('DataTable AJAX Error:', error);
        //                     callback({
        //                         draw: data.draw,
        //                         recordsTotal: 0,
        //                         recordsFiltered: 0,
        //                         data: []
        //                     });
        //                     Swal.fire('Error', 'Failed to load discharge data', 'error');
        //                 }
        //             });
        //         },
        //         columns: [{
        //                 data: 'patient',
        //                 render: function(data, type, row) {
        //                     let profileImage = data ? (data.profile || 'default.png') : 'default.png';
        //                     let patientName = data ? (data.fullname ?? "N/A") : "N/A";
        //                     return `
        //                         <img width="40" height="40" src="${profileImage}" class="rounded-circle">
        //                         <span>${ucfirst(patientName)}</span>
        //                     `;
        //                 },
        //                 className: "view-discharge",
        //                 createdCell: function(td, cellData, rowData) {
        //                     $(td).attr('data-id', rowData.id);
        //                 }
        //             },
        //             {
        //                 data: 'discharge_date',
        //                 render: data => ucfirst(data ?? "N/A"),
        //                 className: "d-none d-md-table-cell"
        //             },
        //             {
        //                 data: 'total_bill',
        //                 render: data => `₹${data ?? "0"}`,
        //                 className: "d-none d-md-table-cell"
        //             },
        //             {
        //                 data: 'payment_status',
        //                 render: data => ucfirst(data ?? "N/A"),
        //                 className: "d-none d-md-table-cell"
        //             },
        //             @if ($razorpayEnabled)
        //                 {
        //                     data: 'id',
        //                     render: function(data, type, row) {
        //                         return `
        //                         <button class="btn btn-sm btn-outline-success generate-discharge-link-btn"
        //                             data-id="${row.id}"
        //                             data-amount="${row.total_bill}"
        //                             data-status="${row.payment_status}">
        //                             <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
        //                         </button>
        //                     `;
        //                     },
        //                     className: "d-none d-md-table-cell"
        //                 },
        //             @endif {
        //                 data: 'id',
        //                 render: function(data) {
        //                     return `
        //                         <div class="icon">
        //                             <i class="fa fa-eye m-r-5 view-discharge" data-id="${data}"></i>
        //                             <i class="fa fa-pencil m-r-5 edit-discharge" data-id="${data}"></i>
        //                             <i class="fa fa-trash-o m-r-5 delete-discharge" data-id="${data}"></i>
        //                         </div>
        //                     `;
        //                 },
        //                 className: "d-none d-md-table-cell"
        //             },
        //             {
        //                 data: null,
        //                 render: function() {
        //                     return `
        //                         <button class="expand-btn">
        //                             <i class="fa fa-chevron-down"></i>
        //                         </button>
        //                     `;
        //                 },
        //                 className: "d-table-cell d-md-none text-center",
        //                 orderable: false
        //             }
        //         ],
        //         order: [
        //             [1, 'desc']
        //         ],
        //         pageLength: 10,
        //         initComplete: function() {
        //             console.log('DataTable initialized successfully');
        //         },
        //         language: {
        //             error: function(xhr, error, code) {
        //                 console.log('DataTable error:', error);
        //                 return "Unable to load data. Please try again.";
        //             }
        //         }
        //     });
        // }

        function fetchdischarge() {
            let branchId = localStorage.getItem('selectedBranchId');

            // Check if DataTable already exists and destroy it properly
            if ($.fn.DataTable.isDataTable("#dischargetbl")) {
                try {
                    // Get existing instance
                    let existingTable = $('#dischargetbl').DataTable();

                    // Destroy the table completely
                    existingTable.destroy();

                    // Clean up DOM elements
                    $('#dischargetbl').removeClass('dataTable');
                    $('#dischargetbl tbody').empty();

                    // Remove any DataTable stored data
                    $.removeData($('#dischargetbl')[0], 'DataTable');
                    $.removeData($('#dischargetbl')[0], 'DataTables_DataTable');

                    // Clear any event handlers
                    $('#dischargetbl').off();

                    // console.log('Existing DataTable destroyed successfully');
                } catch (e) {
                    // console.log('Error destroying table, proceeding with cleanup:', e);
                    // Force cleanup
                    $('#dischargetbl tbody').empty();
                    $.removeData($('#dischargetbl')[0]);
                }
            }

            // Ensure tbody exists
            if ($('#dischargetbl tbody').length === 0) {
                $('#dischargetbl').append('<tbody></tbody>');
            }

            // Small delay to ensure DOM is clean
            setTimeout(function() {
                dischargeTable = $('#dischargetbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true, // Add this to handle reinitialization
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: "{{ url('/api/patient-discharge-details') }}",
                            type: "GET",
                            data: {
                                branch_id: branchId,
                                user_id: userId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(json) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: json.dischargeDetails || []
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
                                Swal.fire('Error', 'Failed to load discharge data',
                                    'error');
                            }
                        });
                    },
                    columns: [{
                            data: 'patient',
                            render: function(data, type, row) {
                                let profileImage = data ? (data.profile || 'default.png') :
                                    'default.png';
                                let patientName = data ? (data.fullname ?? "N/A") : "N/A";
                                return `
                            <img width="40" height="40" src="${profileImage}" class="rounded-circle">
                            <span>${ucfirst(patientName)}</span>
                        `;
                            },
                            className: "view-discharge",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'discharge_date',
                            render: data => ucfirst(data ?? "N/A"),
                            className: "d-none d-md-table-cell"
                        },
                        {
                            data: 'total_bill',
                            render: data => `₹${data ?? "0"}`,
                            className: "d-none d-md-table-cell"
                        },
                        {
                            data: 'payment_status',
                            render: data => ucfirst(data ?? "N/A"),
                            className: "d-none d-md-table-cell"
                        },
                        @if ($razorpayEnabled)
                            {
                                data: 'id',
                                render: function(data, type, row) {
                                    return `
                            <button class="btn btn-sm btn-outline-success generate-discharge-link-btn"
                                data-id="${row.id}"
                                data-amount="${row.total_bill}"
                                data-status="${row.payment_status}">
                                <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                            </button>
                        `;
                                },
                                className: "d-none d-md-table-cell"
                            },
                        @endif {
                            data: 'id',
                            render: function(data) {
                                return `
                            <div class="icon">
                                <i class="fa fa-eye m-r-5 view-discharge" data-id="${data}"></i>
                                <i class="fa fa-pencil m-r-5 edit-discharge" data-id="${data}"></i>
                                <i class="fa fa-trash-o m-r-5 delete-discharge" data-id="${data}"></i>
                            </div>
                        `;
                            },
                            className: "d-none d-md-table-cell"
                        },
                        {
                            data: null,
                            render: function() {
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
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 10,
                    initComplete: function() {
                        // console.log('DataTable initialized successfully');
                    },
                    language: {
                        error: function(xhr, error, code) {
                            // console.log('DataTable error:', error);
                            return "Unable to load data. Please try again.";
                        }
                    }
                });
            }, 50);
        }

        $(document).on('click', '.expand-btn', function() {
            let btn = $(this);
            let icon = btn.find('i');
            let tr = btn.closest('tr');
            let row = dischargeTable.row(tr);
            let data = row.data();
            let nextRow = tr.next('.details-row');

            if (nextRow.length) {
                nextRow.slideToggle(300);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
                return;
            }

            let dischargeDate = ucfirst(data.discharge_date ?? "N/A");
            let bill = `₹${data.total_bill ?? "0"}`;
            let payment = ucfirst(data.payment_status ?? "N/A");

            let generateBtn = '';
            @if ($razorpayEnabled)
                generateBtn = `
                <button class="btn btn-sm btn-outline-success generate-discharge-link-btn"
                    data-id="${data.id}"
                    data-amount="${data.total_bill}"
                    data-status="${data.payment_status}">
                    <i class="fa fa-link"></i> <span class="hdr-btn-text">Generate</span>
                </button>
            `;
            @endif

            let actions = `
                <div class="icon">
                    <i class="fa fa-eye m-r-5 view-discharge" data-id="${data.id}"></i>
                    <i class="fa fa-pencil m-r-5 edit-discharge" data-id="${data.id}"></i>
                    <i class="fa fa-trash-o m-r-5 delete-discharge" data-id="${data.id}"></i>
                </div>
            `;

            let detailsRow = `
                                    <tr class="details-row">
                                        <td colspan="7">
                                            <div class="details-content">
                                                <div><strong>Discharge Date:</strong> ${dischargeDate}</div>
                                                <div><strong>Total Bill:</strong> ${bill}</div>
                                                <div><strong>Payment Status:</strong> ${payment}</div>

                                                <div class="mt-2"><strong>Payment Link:</strong><br>${generateBtn}</div>
                                                <div class="mt-2"><strong>Actions:</strong><br>${actions}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    `;


            tr.after(detailsRow);
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        });
        $(window).on('resize', function() {
            // Desktop breakpoint (Bootstrap md = 768px)
            if ($(window).width() >= 768) {

                // Remove all expanded mobile rows
                $('.details-row').remove();

                // Reset all expand icons
                $('.expand-btn i')
                    .removeClass('fa-chevron-up')
                    .addClass('fa-chevron-down');
            }
        });



        $(document).on('click', '.generate-discharge-link-btn', function() {

            let dischargeId = $(this).data('id');
            let amount = $(this).data('amount');
            let status = $(this).data('status'); // paid / unpaid

            // ✅ If already paid
            if (status === 'paid') {
                Swal.fire({
                    icon: 'info',
                    title: 'No Pending Amount',
                    text: 'This discharge payment is already completed.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // ❌ Amount check
            if (!amount || amount <= 0) {
                Swal.fire('Info', 'No payable amount found.', 'info');
                return;
            }

            // ✅ Generate payment link
            $.ajax({
                url: `/api/razorpay/discharge/generate-payment-link`,
                type: 'POST',
                data: {
                    discharge_id: dischargeId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {

                    navigator.clipboard.writeText(res.payment_link);

                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Link Generated',
                        html: `
                                                <p><strong>Patient Name:</strong> ${res.patient_name}</p>
                                                <p><strong>Contact No:</strong> ${res.patient_phone}</p>
                                                <p><strong>Amount:</strong> ₹${res.amount}</p>
                                            <input 
                                                    type="text"
                                                    value="${res.payment_link}"
                                                    readonly
                                                    style="
                                                        width:100%;
                                                        padding:12px;
                                                        margin-top:15px;
                                                        border-radius:25px;
                                                        border: 1.5px solid #ff8c6b;
                                                        text-align:left;
                                                        font-size:20px;
                                                        background:#fff;
                                                        color: #4a4a4a;
                                                    "
                                                >

                                                <div style="text-align:center; font-size:13px; color:#666; margin-top:6px;">
                                                    Link copied to clipboard ✔
                                                </div>
                                            </div>
                                        `,
                        showCancelButton: true,
                        showConfirmButton: false, // remove OK
                        cancelButtonText: 'Close',
                        customClass: {
                            cancelButton: 'swal-cancel-btn'
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || 'Failed to generate payment link';
                    Swal.fire({
                        icon: 'error', // can also use 'info' or 'warning'
                        title: 'Error',
                        text: message,
                        showConfirmButton: false, // hide OK button
                        showCancelButton: true, // show Cancel button instead
                        cancelButtonText: 'Close',
                        cancelButtonColor: '#f89884',
                        customClass: {
                            cancelButton: 'swal-cancel-btn' // apply your custom style
                        }
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        });





        $(document).on('click', '.view-discharge', function() {
            var dischargeId = $(this).data('id');
            window.location.href = '/discharge/show/' + dischargeId;
        });

        $(document).on('click', '.edit-discharge', function() {
            var dischargeId = $(this).data('id');
            window.location.href = '/discharge/edit/' + dischargeId;
        });

        $(document).on('click', '.delete-discharge', function() {
            var dischargeId = $(this).data('id');

            if (!dischargeId) {
                Swal.fire('Error', 'Patient ID not found!', 'error');
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
                    confirmButton: 'swal-confirm-btn', // ✅ custom class
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/patient-discharge-details/' + dischargeId,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Patient discharge details deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload(); // Reload the page to update the list
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            Swal.fire('Error', 'Failed to delete patient discharge details.',
                                'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
