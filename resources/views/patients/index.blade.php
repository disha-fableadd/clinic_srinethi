@extends('layout.app')
<style>
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
    }

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    i.fa.fa-eye.m-r-5.icon3.view-patient,
    i.fa.fa-pencil.m-r-5.icon1.edit-patient,
    i.fa.fa-trash-o.m-r-5.icon2.delete-patient,
    i.fa.fa-download.m-r-5.icon4.download-patient-history {
        cursor: pointer;
    }

    #loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
        background: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 5px;
    }

    .card-header {
        background-color: #f89884 !important;
    }

    .btn-rounded {
        background-color: #fed9cf !important;
    }


    @media screen and (max-width: 767px) {
        .card .card-header {
            padding: 10px 12px !important;
        }
    }


    @media (min-width: 768px) {
        .mobile-only {
            display: none !important;
        }

        .details-row {
            display: none !important;
        }
    }

    @media (max-width: 767px) {
        .desktop-only {
            display: none !important;
        }

        td img {
            width: 35px;
            height: 35px;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-wheelchair px-2" style="font-size:20px"></i>All Patients
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(5, 'create'))
                                <a href="{{ route('patients.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                            @if (app('hasPermission')(17, 'create'))
                                <a href="{{ route('patient_medicine.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-pills"></i> <span class="hdr-btn-text">Prescription</span>
                                </a>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="row mb-3 m-1">

                            <!-- Patient Type Filter -->
                            <div class="col-md-4 col-sm-6 col-12 mt-2">
                                <label>Patient Type</label>
                                <select id="patientTypeFilter" class="form-control filter">
                                    <option value="">All</option>
                                    <option value="All">All Patients</option>
                                    <option value="Home">Home Patient</option>
                                    <option value="OPD">OPD Patient</option>
                                    <option value="IPD">IPD Patient</option>
                                    <option value="Cosmetic">Cosmetic Patient</option>
                                </select>
                            </div>

                            <!-- Year Filter -->
                            <div class="col-md-4 col-sm-6 col-6 mt-2">
                                <label>Year</label>
                                <select id="filterYear" class="form-control yearfilter select2 filter">
                                    <option value="">All</option>
                                    {{-- Options can be appended dynamically via JS if needed --}}
                                </select>
                            </div>

                            <!-- Month Filter -->
                            <div class="col-md-4 col-sm-6 col-6 mt-2">
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


                        </div>


                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="patienttbl" class="table custom-table">
                                    <div id="loader" class="text-center" style="display: none;">
                                        <div class="spinner-border text-primary" role="status"
                                            style="width: 3rem; height: 3rem;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>

                                    </div>

                                    <thead style="background-color:#ff8e29;">
                                        <tr>
                                            <th style="display: none;">ID</th>
                                            <th>Patient ID</th>
                                            <th>Patients</th>

                                            <!-- <th>Age</th> -->
                                            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                                <th class="d-none d-md-table-cell">Contact</th>
                                            @endif
                                            <th class="d-none d-md-table-cell">Referral Source</th>
                                            <th class="d-none d-md-table-cell">Referral Name</th>
                                            <th class="d-none d-md-table-cell">Diagnosis</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientbody">
                                        <!-- Data will be appended here via jQuery AJAX -->
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>




    <script>
        $('.yearfilter').select2({

            width: '100%',
            placeholder: "Select Year",
            allowClear: true,
            minimumResultsForSearch: Infinity
        });
        $('.yearfilter').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Year');
        });


        $('.selectmonth').select2({
            placeholder: "Select Month",
            allowClear: true,
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
        $('.selectmonth').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search month');
        });
        $('#patientTypeFilter').select2({
            placeholder: "Select Patient type",
            allowClear: true,
            width: '100%'
        });
        $('#patientTypeFilter').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search Patient type');
        });

        $.ajax({
            url: "/api/patients/years",
            type: "GET",
            headers: {
                "Authorization": "Bearer " + token
            },
            success: function(data) {
                let yearSelect = $('#filterYear');
                yearSelect.empty().append('<option value="">All</option>'); // default option
                data.years.forEach(function(year) {
                    yearSelect.append(`<option value="${year}">${year}</option>`);
                });
            }
        });


        // $(document).ready(function() {
        //     let branchId = localStorage.getItem('selectedBranchId');

        //     // ✅ Fetch patients
        //     function fetchPatients() {
        //         let branchId = localStorage.getItem('selectedBranchId');

        //         if ($.fn.DataTable.isDataTable("#patienttbl")) {
        //             $('#patienttbl').DataTable().destroy();
        //         }

        //         $('#patienttbl').DataTable({
        //             processing: true,
        //             serverSide: true,
        //             ajax: function(data, callback) {
        //                 const page = Math.floor(data.start / data.length) + 1;
        //                 const patientType = $('#patientTypeFilter').val();
        //                 const year = $('#filterYear').val();
        //                 const month = $('#filterMonth').val();

        //                 $.ajax({
        //                     url: "{{ url('/api/patient') }}",
        //                     type: "GET",
        //                     data: {
        //                         branch_id: branchId,
        //                         type: patientType,
        //                         year: year,
        //                         month: month,
        //                         page: page,
        //                         per_page: data.length,
        //                         search: data.search?.value || ''
        //                     },
        //                     dataType: "json",
        //                     headers: {
        //                         "Authorization": "Bearer " + token
        //                     },
        //                     success: function(response) {
        //                         callback({
        //                             draw: data.draw,
        //                             recordsTotal: response.pagination?.total || 0,
        //                             recordsFiltered: response.pagination?.total || 0,
        //                             data: response.patients || []
        //                         });
        //                     },
        //                     error: function() {
        //                         callback({
        //                             draw: data.draw,
        //                             recordsTotal: 0,
        //                             recordsFiltered: 0,
        //                             data: []
        //                         });
        //                     }
        //                 });
        //             },
        //             columns: [
        //                 { data: 'id', visible: false, searchable: false },
        //                 { data: 'patient_unique_id', render: data => data || 'N/A' },
        //                 {
        //                     data: 'fullname',
        //                     render: function(data, type, row) {
        //                         let defaultImage = "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";
        //                         let imageUrl = row.profile || defaultImage;
        //                         let fullName = data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A';
        //                         return `
    //                             <img width="50" height="50" src="${imageUrl}" class="rounded-circle" alt="">
    //                             <span><h2>${fullName}</h2></span>
    //                         `;
        //                     }
        //                 },
        //                 @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
        //                 { 
        //                     data: 'phone', 
        //                     className: 'd-none d-md-table-cell',
        //                     render: data => data || 'N/A' 
        //                 },
        //                 @endif
        //                 { 
        //                     data: 'referral_source', 
        //                     className: 'd-none d-md-table-cell',
        //                     render: data => data || 'N/A' 
        //                 },
        //                 { 
        //                     data: 'source_details', 
        //                     className: 'd-none d-md-table-cell',
        //                     render: function(data, type, row) {
        //                         if (row.referral_source === "Doctors") {
        //                             return data?.referral_name ? data.referral_name : 'N/A';
        //                         } else if (data) {
        //                             let details = Object.entries(data)
        //                                 .map(([k, v]) => v ? v : '')
        //                                 .join('<br>');
        //                             return details.trim() === '' ? 'N/A' : details;
        //                         }
        //                         return 'N/A';
        //                     }
        //                 },
        //                 { 
        //                     data: 'diagnosis_name', 
        //                     className: 'd-none d-md-table-cell',
        //                     render: data => data ? data.charAt(0).toUpperCase() + data.slice(1) : 'No diagnosis' 
        //                 },
        //                 {
        //                     data: null,
        //                     className: 'd-none d-md-table-cell',
        //                     orderable: false,
        //                     render: function(data, type, row) {
        //                         let actions = `<div class="icon">`;
        //                         @if (app('hasPermission')(5, 'view'))
        //                             actions += `<i class="fa fa-eye m-r-5 icon3 view-patient" data-id="${row.id}" title="View Patient"></i>`;
        //                         @endif
        //                         @if (app('hasPermission')(5, 'update'))
        //                             actions += `<i class="fa fa-pencil m-r-5 icon1 edit-patient" data-id="${row.id}" title="Edit Patient"></i>`;
        //                         @endif
        //                         @if (app('hasPermission')(5, 'delete'))
        //                             actions += `<i class="fa fa-trash-o m-r-5 icon2 delete-patient" data-id="${row.id}" title="Delete Patient"></i>`;
        //                         @endif
        //                         @if (app('hasPermission')(5, 'view'))
        //                             actions += `<i class="fa fa-download m-r-5 icon4 download-patient-history" data-id="${row.id}" title="Download History PDF"></i>`;
        //                         @endif
        //                         actions += `</div>`;
        //                         return actions;
        //                     }
        //                 },
        //                 {
        //                     data: null,
        //                     className: 'd-table-cell d-md-none text-center',
        //                     orderable: false,
        //                     render: function(data, type, row) {
        //                         return `
    //                             <button class="btn btn-link expand-btn" data-id="${row.id}">
    //                                 <i class="fa fa-chevron-down"></i>
    //                             </button>
    //                         `;
        //                     }
        //                 }
        //             ],
        //             order: [[0, "desc"]],
        //             pageLength: 10
        //         });

        //         // Bind expand button events
        //         $(document).off('click', '.expand-btn').on('click', '.expand-btn', function() {
        //             var patientId = $(this).data('id');
        //             var currentRow = $(this).closest('tr');
        //             var table = $('#patienttbl').DataTable();
        //             var row = table.row(currentRow);
        //             var data = row.data();
        //             var detailsRow = currentRow.next('.details-row');

        //             if (!detailsRow.length) {
        //                 let actionsHtml = '';
        //                 @if (app('hasPermission')(5, 'view'))
        //                     actionsHtml += `<i class="fa fa-eye m-r-5 icon3 view-patient" data-id="${data.id}" title="View Patient"></i>`;
        //                 @endif
        //                 @if (app('hasPermission')(5, 'update'))
        //                     actionsHtml += `<i class="fa fa-pencil m-r-5 icon1 edit-patient" data-id="${data.id}" title="Edit Patient"></i>`;
        //                 @endif
        //                 @if (app('hasPermission')(5, 'delete'))
        //                     actionsHtml += `<i class="fa fa-trash-o m-r-5 icon2 delete-patient" data-id="${data.id}" title="Delete Patient"></i>`;
        //                 @endif
        //                 @if (app('hasPermission')(5, 'view'))
        //                     actionsHtml += `<i class="fa fa-download m-r-5 icon4 download-patient-history" data-id="${data.id}" title="Download History PDF"></i>`;
        //                 @endif

        //                 detailsRow = $(`<tr class="details-row" data-id="${patientId}">
    //                     <td colspan="8">
    //                         <div class="details-content">
    //                             <div><strong>Contact:</strong> ${data.phone || 'N/A'}</div>
    //                             <div><strong>Referral Source:</strong> ${data.referral_source || 'N/A'}</div>
    //                             <div><strong>Diagnosis:</strong> ${data.diagnosis_name || 'No diagnosis'}</div>
    //                             <div class="mt-2">
    //                                 <strong>Action:</strong>
    //                                 <div class="icon">
    //                                     ${actionsHtml}
    //                                 </div>
    //                             </div>
    //                         </div>
    //                     </td>
    //                 </tr>`);
        //                 currentRow.after(detailsRow.hide());
        //             }

        //             var icon = $(this).find('i');
        //             if (detailsRow.is(':visible')) {
        //                 detailsRow.slideUp(300, function() {
        //                     icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        //                 });
        //             } else {
        //                 detailsRow.slideDown(300);
        //                 icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        //             }
        //         });
        //     }

        //     function updateFilters() {
        //         if ($.fn.DataTable.isDataTable("#patienttbl")) {
        //             $('#patienttbl').DataTable().ajax.reload();
        //         } else {
        //             fetchPatients();
        //         }
        //     }

        //     // Initial load
        //     updateFilters();

        //     $('#patientTypeFilter, #filterYear, #filterMonth').on('change', function() {
        //         updateFilters();
        //     });
        //     // ✅ Export button with branch filter
        //     $(document).on('click', '#exportButton', function() {
        //         let branchId = localStorage.getItem("selectedBranchId");
        //         if (!branchId) {
        //             alert("Please select a branch first.");
        //             return;
        //         }
        //         window.location.href = "{{ route('patient.export') }}" + "?branch_id=" + branchId;
        //     });
        //     // ✅ Mobile expand button handler
        //     // ✅ Mobile expand button handler (fixed)
        //     $(document).on('click', '.expand-btn', function() {
        //         const btn = $(this);
        //         const tr = btn.closest('tr');
        //         const icon = btn.find('i');

        //         // Collapse any other open details
        //         $('.details-row').not(tr.next('.details-row')).remove();
        //         $('.expand-btn i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

        //         // If already open, close it
        //         if (tr.next().hasClass('details-row')) {
        //             tr.next('.details-row').remove();
        //             return;
        //         }

        //         // Extract values safely (handle hidden columns)
        //         const tds = tr.find('td');
        //         const patientId = btn.data('id');
        //         const patientName = tds.eq(2).text().trim() || 'N/A';
        //         const contact = tds.eq(3).text()?.trim() || 'N/A';
        //         const referralSource = tds.eq(4).text()?.trim() || 'N/A';
        //         const referralName = tds.eq(5).text()?.trim() || 'N/A';
        //         const diagnosis = tds.eq(6).text()?.trim() || 'N/A';

        //         // Build a clean detail row
        //         const detailRow = $(`
    //                 <tr class="details-row d-md-none">
    //                     <td colspan="8">
    //                         <div class="details-content">
    //                             <div><strong>Contact:</strong> ${contact}</div>
    //                             <div><strong>Referral Source:</strong> ${referralSource}</div>
    //                             <div><strong>Referral Name:</strong> ${referralName}</div>
    //                             <div><strong>Diagnosis:</strong> ${diagnosis}</div>
    //                             <div class="mt-2 icon">
    //                                 @if (app('hasPermission')(5, 'view'))
    //                                     <i class="fa fa-eye m-r-5 icon3 view-patient" data-id="${patientId}" title="View Patient"></i>
    //                                 @endif
    //                                 @if (app('hasPermission')(5, 'update'))
    //                                     <i class="fa fa-pencil m-r-5 icon1 edit-patient" data-id="${patientId}" title="Edit Patient"></i>
    //                                 @endif
    //                                 @if (app('hasPermission')(5, 'delete'))
    //                                     <i class="fa fa-trash-o m-r-5 icon2 delete-patient" data-id="${patientId}" title="Delete Patient"></i>
    //                                 @endif
    //                                 @if (app('hasPermission')(5, 'view'))
    //                                     <i class="fa fa-download m-r-5 icon4 download-patient-history" data-id="${patientId}" title="Download History PDF"></i>
    //                                 @endif
    //                             </div>
    //                         </div>
    //                     </td>
    //                 </tr>
    //             `);

        //         tr.after(detailRow);
        //         icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        //     });


        //     // ✅ View patient
        //     $(document).on('click', '.view-patient', function() {
        //         let patientId = $(this).data('id');
        //         window.location.href = '/patient/show/' + patientId;
        //     });

        //     // ✅ Edit patient
        //     $(document).on('click', '.edit-patient', function() {
        //         let patientId = $(this).data('id');
        //         window.location.href = '/patient/edit/' + patientId;
        //     });

        //     // ✅ Delete patient
        //     $(document).on('click', '.delete-patient', function() {
        //         let patientId = $(this).data('id');
        //         if (!patientId) {
        //             Swal.fire('Error', 'Patient ID not found!', 'error');
        //             return;
        //         }

        //         Swal.fire({
        //             title: 'Are you sure?',
        //             text: "You won't be able to revert this!",
        //             icon: 'warning',
        //             showCancelButton: true,
        //             confirmButtonColor: '#cfece0',
        //             cancelButtonColor: '#f89884',
        //             confirmButtonText: 'Yes, delete it!',
        //             cancelButtonText: 'Cancel',
        //             customClass: {
        //                 confirmButton: 'swal-confirm-btn',
        //                 cancelButton: 'swal-cancel-btn'
        //             }
        //         }).then((result) => {
        //             if (result.isConfirmed) {
        //                 $.ajax({
        //                     url: '/api/patient/' + patientId,
        //                     type: 'DELETE',
        //                     success: function(response) {
        //                         Swal.fire({
        //                             title: 'Deleted!',
        //                             text: 'Patient deleted successfully!',
        //                             icon: 'success',
        //                             timer: 1500,
        //                             showConfirmButton: false
        //                         }).then(() => {
        //                             location.reload();
        //                         });
        //                     },
        //                     error: function(xhr) {
        //                         Swal.fire('Error', xhr.responseJSON?.message ||
        //                             'Failed to delete patient. Please try again.',
        //                             'error');
        //                     }
        //                 });
        //             }
        //         });
        //     });

        //     // ✅ Download patient history
        //     $(document).on('click', '.download-patient-history', function() {
        //         let patientId = $(this).data('id');
        //         window.location.href = `/api/patients/${patientId}/history-pdf`;
        //     });
        // });

        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');

            // ✅ Fetch patients
            function fetchPatients() {
                let branchId = localStorage.getItem('selectedBranchId');

                // Properly destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable("#patienttbl")) {
                    try {
                        let existingTable = $('#patienttbl').DataTable();
                        existingTable.destroy();
                        $('#patienttbl').removeClass('dataTable');
                        $('#patienttbl tbody').empty();
                        $.removeData($('#patienttbl')[0], 'DataTable');
                        $.removeData($('#patienttbl')[0], 'DataTables_DataTable');
                        $('#patienttbl').off();
                    } catch (e) {
                        $('#patienttbl tbody').empty();
                        $.removeData($('#patienttbl')[0]);
                    }
                }

                // Ensure tbody exists
                if ($('#patienttbl tbody').length === 0) {
                    $('#patienttbl').append('<tbody></tbody>');
                }

                // Small delay to ensure DOM is clean
                setTimeout(function() {
                    $('#patienttbl').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: function(data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const patientType = $('#patientTypeFilter').val();
                            const year = $('#filterYear').val();
                            const month = $('#filterMonth').val();

                            $.ajax({
                                url: "{{ url('/api/patient') }}",
                                type: "GET",
                                data: {
                                    branch_id: branchId,
                                    type: patientType,
                                    year: year,
                                    month: month,
                                    page: page,
                                    per_page: data.length,
                                    search: data.search?.value || ''
                                },
                                dataType: "json",
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function(response) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination
                                            ?.total || 0,
                                        recordsFiltered: response.pagination
                                            ?.total || 0,
                                        data: response.patients || []
                                    });
                                },
                                error: function() {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: 0,
                                        recordsFiltered: 0,
                                        data: []
                                    });
                                }
                            });
                        },
                        columns: [{
                                data: 'id',
                                visible: false,
                                searchable: false
                            },
                            {
                                data: 'patient_unique_id',
                                render: data => data || 'N/A'
                            },
                            {
                                data: 'fullname',
                                render: function(data, type, row) {
                                    let defaultImage =
                                        "{{ asset(env('IMAGE_PATH') . 'admin/assets/img/img1.png') }}";
                                    let imageUrl = row.profile || defaultImage;
                                    let fullName = data ? data.charAt(0).toUpperCase() +
                                        data.slice(1) : 'N/A';
                                    return `
                                <img width="50" height="50" src="${imageUrl}" class="rounded-circle" alt="">
                                <span><h2>${fullName}</h2></span>
                            `;
                                }
                            },
                            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                                {
                                    data: 'phone',
                                    className: 'd-none d-md-table-cell',
                                    render: data => data || 'N/A'
                                },
                            @endif {
                                data: 'referral_source',
                                className: 'd-none d-md-table-cell',
                                render: data => data || 'N/A'
                            },
                            {
                                data: 'source_details',
                                className: 'd-none d-md-table-cell',
                                render: function(data, type, row) {
                                    if (row.referral_source === "Doctors") {
                                        return data?.referral_name ? data.referral_name :
                                            'N/A';
                                    } else if (data) {
                                        let details = Object.entries(data)
                                            .map(([k, v]) => v ? v : '')
                                            .join('<br>');
                                        return details.trim() === '' ? 'N/A' : details;
                                    }
                                    return 'N/A';
                                }
                            },
                            {
                                data: 'diagnosis_name',
                                className: 'd-none d-md-table-cell',
                                render: data => data ? data.charAt(0).toUpperCase() + data
                                    .slice(1) : 'No diagnosis'
                            },
                            {
                                data: null,
                                className: 'd-none d-md-table-cell',
                                orderable: false,
                                render: function(data, type, row) {
                                    let actions = `<div class="icon">`;
                                    @if (app('hasPermission')(5, 'view'))
                                        actions +=
                                            `<i class="fa fa-eye m-r-5 icon3 view-patient" data-id="${row.id}" title="View Patient"></i>`;
                                    @endif
                                    @if (app('hasPermission')(5, 'update'))
                                        actions +=
                                            `<i class="fa fa-pencil m-r-5 icon1 edit-patient" data-id="${row.id}" title="Edit Patient"></i>`;
                                    @endif
                                    @if (app('hasPermission')(5, 'delete'))
                                        actions +=
                                            `<i class="fa fa-trash-o m-r-5 icon2 delete-patient" data-id="${row.id}" title="Delete Patient"></i>`;
                                    @endif
                                    @if (app('hasPermission')(5, 'view'))
                                        actions +=
                                            `<i class="fa fa-download m-r-5 icon4 download-patient-history" data-id="${row.id}" title="Download History PDF"></i>`;
                                    @endif
                                    actions += `</div>`;
                                    return actions;
                                }
                            },
                            {
                                data: null,
                                className: 'd-table-cell d-md-none text-center',
                                orderable: false,
                                render: function(data, type, row) {
                                    return `
                                <button class="btn btn-link expand-btn" data-id="${row.id}">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            `;
                                }
                            }
                        ],
                        order: [
                            [0, "desc"]
                        ],
                        pageLength: 10,
                        language: {
                            processing: '<div></div>',
                            emptyTable: "No data available"
                        }
                    });

                    // Bind expand button events
                    $(document).off('click', '.expand-btn').on('click', '.expand-btn', function() {
                        var patientId = $(this).data('id');
                        var currentRow = $(this).closest('tr');
                        var table = $('#patienttbl').DataTable();
                        var row = table.row(currentRow);
                        var data = row.data();
                        var detailsRow = currentRow.next('.details-row');

                        if (!detailsRow.length) {
                            let actionsHtml = '';
                            @if (app('hasPermission')(5, 'view'))
                                actionsHtml +=
                                    `<i class="fa fa-eye m-r-5 icon3 view-patient" data-id="${data.id}" title="View Patient"></i>`;
                            @endif
                            @if (app('hasPermission')(5, 'update'))
                                actionsHtml +=
                                    `<i class="fa fa-pencil m-r-5 icon1 edit-patient" data-id="${data.id}" title="Edit Patient"></i>`;
                            @endif
                            @if (app('hasPermission')(5, 'delete'))
                                actionsHtml +=
                                    `<i class="fa fa-trash-o m-r-5 icon2 delete-patient" data-id="${data.id}" title="Delete Patient"></i>`;
                            @endif
                            @if (app('hasPermission')(5, 'view'))
                                actionsHtml +=
                                    `<i class="fa fa-download m-r-5 icon4 download-patient-history" data-id="${data.id}" title="Download History PDF"></i>`;
                            @endif

                            detailsRow = $(`<tr class="details-row" data-id="${patientId}">
                        <td colspan="8">
                            <div class="details-content">
                                <div><strong>Contact:</strong> ${data.phone || 'N/A'}</div>
                                <div><strong>Referral Source:</strong> ${data.referral_source || 'N/A'}</div>
                                <div><strong>Diagnosis:</strong> ${data.diagnosis_name || 'No diagnosis'}</div>
                                <div class="mt-2">
                                    <strong>Action:</strong>
                                    <div class="icon">
                                        ${actionsHtml}
                                    </div>
                                </div>
                            </div>
                        </td>
                     </tr>`);
                            currentRow.after(detailsRow.hide());
                        }

                        var icon = $(this).find('i');
                        if (detailsRow.is(':visible')) {
                            detailsRow.slideUp(300, function() {
                                icon.removeClass('fa-chevron-up').addClass(
                                    'fa-chevron-down');
                            });
                        } else {
                            detailsRow.slideDown(300);
                            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                        }
                    });
                }, 50);
            }

            function updateFilters() {
                if ($.fn.DataTable.isDataTable("#patienttbl")) {
                    $('#patienttbl').DataTable().ajax.reload();
                } else {
                    fetchPatients();
                }
            }

            // ✅ Initial load - ONLY call fetchPatients once
            fetchPatients();

            // Bind filter change events
            $('#patientTypeFilter, #filterYear, #filterMonth').on('change', function() {
                updateFilters();
            });

            // ✅ Export button with branch filter
            $(document).on('click', '#exportButton', function() {
                let branchId = localStorage.getItem("selectedBranchId");
                if (!branchId) {
                    alert("Please select a branch first.");
                    return;
                }
                window.location.href = "{{ route('patient.export') }}" + "?branch_id=" + branchId;
            });

            // ✅ View patient
            $(document).on('click', '.view-patient', function() {
                let patientId = $(this).data('id');
                window.location.href = '/patient/show/' + patientId;
            });

            // ✅ Edit patient
            $(document).on('click', '.edit-patient', function() {
                let patientId = $(this).data('id');
                window.location.href = '/patient/edit/' + patientId;
            });

            // ✅ Delete patient
            $(document).on('click', '.delete-patient', function() {
                let patientId = $(this).data('id');
                if (!patientId) {
                    Swal.fire('Error', 'Patient ID not found!', 'error');
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
                            url: '/api/patient/' + patientId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Patient deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Failed to delete patient. Please try again.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // ✅ Download patient history
            $(document).on('click', '.download-patient-history', function() {
                let patientId = $(this).data('id');
                window.location.href = `/api/patients/${patientId}/history-pdf`;
            });
        });
    </script>
@endsection

