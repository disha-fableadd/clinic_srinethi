@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
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
    .card-header{
        background-color:#f89884 !important;
    }
    .btn-rounded{
        background-color: #fed9cf !important;
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
</style>
@php
    $isPatientRole = optional(Auth::user()?->role)->name === 'Patient';
@endphp
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">


            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white"><i class="fa fa-calendar px-2"
                                    style="font-size:20px"></i>All Followup </h3>
                            @if (!$isPatientRole)
                                <button class="btn btn-rounded btn-hdr" id="exportButton">
                                    <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                                </button>
                            @endif
                            @if (app('hasPermission')(2, 'create') && !$isPatientRole)
                                <a href="{{ route('followup.create') }}" class="btn btn-rounded btn-hdr"><i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="followuptbl" class="table  custom-table">
                                    <thead style="background-color:#ff8e6;" class="text-center">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            @php
                                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                                            @endphp
                                            @if($currentProjectTypeId !== 3)
                                            <th class="d-none d-md-table-cell">Treatment</th>
                                            @endif
                                            <th class="d-none d-md-table-cell">Followup Date</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- âœ… For mobile -->
                                        </tr>
                                    </thead>
                                    <tbody class="followuptbl">

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
    let branchId = localStorage.getItem('selectedBranchId');
    let followupTable;

    $(document).on('click', '#exportButton', function() {
        let branchId = localStorage.getItem("selectedBranchId");

        if (!branchId) {
            alert("Please select a branch first.");
            return;
        }

        // âœ… Redirect with branch_id in query string
        window.location.href = "{{ route('followup.export') }}" + "?branch_id=" + branchId;
    });

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggle_btn');
        const sidebar = document.querySelector('.sidebar');

        toggleBtn.addEventListener('click', function() {
            if (sidebar) {
                sidebar.classList.toggle('mini-sidebar');
            }
        });
    });

    $(document).ready(function() {
        // Fetch followup data when page loads
        fetchfollowup();

        function ucfirst(str) {
            if (!str) return 'N/A';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function fetchfollowup() {
            let branchId = localStorage.getItem('selectedBranchId');

            if ($.fn.DataTable.isDataTable("#followuptbl")) {
                try {
                    let existingTable = $('#followuptbl').DataTable();
                    existingTable.destroy();
                    $('#followuptbl').removeClass('dataTable');
                    $('#followuptbl tbody').empty();
                    $.removeData($('#followuptbl')[0], 'DataTable');
                    $.removeData($('#followuptbl')[0], 'DataTables_DataTable');
                    $('#followuptbl').off();
                } catch (e) {
                    $('#followuptbl tbody').empty();
                    $.removeData($('#followuptbl')[0]);
                }
            }

            if ($('#followuptbl tbody').length == 0) {
                $('#followuptbl').append('<tbody class="followuptbl"></tbody>');
            }

            setTimeout(function() {
                followupTable = $('#followuptbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: "/api/followup",
                            type: "GET",
                            dataType: "json",
                            data: {
                                branch_id: branchId,
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
                                    data: json.followups || []
                                });
                            },
                            error: function(xhr) {
                                console.error("Failed to load followups", xhr.responseText);
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
                            data: 'patient',
                            render: function(data) {
                                let profileImage = data ? (data.profile || 'default.png') :
                                    'default.png';
                                let patientName = data ? (data.fullname ?? "N/A") : "N/A";
                                return `
                                    <img width="50" height="50" src="${profileImage}" class="rounded-circle" alt="">
                                    <span>${ucfirst(patientName)}</span>
                                `;
                            },
                            className: "view-followup",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'doctor',
                            render: function(data) {
                                const doctor = data || {};
                                return `${ucfirst(doctor.fullname ?? 'N/A')}`;
                            },
                            className: "view-followup",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        @if($currentProjectTypeId !== 3)
                        {
                            data: 'treatment',
                            render: function(data) {
                                const treatment = data || {};
                                return `${ucfirst(treatment.name ?? 'N/A')}`;
                            },
                            className: "d-none d-md-table-cell view-followup",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        @endif
                        {
                            data: 'date',
                            render: data => data ?? 'N/A',
                            className: "d-none d-md-table-cell view-followup",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'id',
                            render: function(data) {
                                return `
                                    <div class="icon" style="cursor:pointer">
                                        @if (app('hasPermission')(2, 'view')) 
                                            <i class="fa fa-eye m-r-5 icon3 view-followup" data-id="${data}" title="View Followup"></i>
                                        @endif 
                                        @if (app('hasPermission')(2, 'update') && !$isPatientRole) 
                                            <i class="fa fa-pencil m-r-5 icon1 edit-followup" data-id="${data}" title="Edit Followup"></i> 
                                        @endif
                                        @if (app('hasPermission')(2, 'delete') && !$isPatientRole) 
                                            <i class="fa fa-trash-o m-r-5 icon2 delete-followup" data-id="${data}" title="Delete Followup"></i> 
                                        @endif
                                        @if (app('hasPermission')(2, 'view')) 
                                            <i class="fa fa-download m-r-5 icon4 download-followup" data-id="${data}" title="Download Followup PDF"></i>
                                        @endif
                                    </div>
                                `;
                            },
                            className: "d-none d-md-table-cell"
                        },
                        {
                            data: null,
                            render: function() {
                                return `
                                    <button class="btn btn-link expand-btn"><i class="fa fa-chevron-down"></i></button>
                                `;
                            },
                            className: "d-table-cell d-md-none text-center",
                            orderable: false
                        }
                    ],
                    order: [
                        [3, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        error: function() {
                            return "Unable to load data. Please try again.";
                        }
                    }
                });
            }, 50);
        }

        $(document).on('click', '.download-followup', function() {
            const followupId = $(this).data('id');
            window.location.href = `/api/followups/${followupId}/download`;
        });

        // View Followup
        $(document).on('click', '.view-followup', function() {
            var followupId = $(this).data('id');
            if (!followupId) {
                return;
            }
            window.location.href = '/followup/show/' + followupId;
        });

        // Edit Followup
        $(document).on('click', '.edit-followup', function() {
            var followupId = $(this).data('id');
            if (!followupId) {
                return;
            }
            window.location.href = '/followup/edit/' + followupId;
        });

        // Delete Followup
        $(document).on('click', '.delete-followup', function() {
            var followupId = $(this).data('id');

            if (!followupId) {
                Swal.fire('Error', 'Followup ID not found!', 'error');
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
                    confirmButton: 'swal-confirm-btn', // âœ… custom class
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/api/followup/' + followupId,
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

    });

    // âœ… Mobile Details Expand
    $(document).on("click", ".expand-btn", function(e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");
        const existingRow = tr.next(".details-row");

        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        let row = followupTable.row(tr);
        let data = row.data() || {};
        const treatment = data?.treatment?.name ?? "N/A";
        const followupDate = data?.date ?? "N/A";

        const actionHtml = `
            <div class="icon" style="cursor:pointer">
                @if (app('hasPermission')(2, 'view')) 
                    <i class="fa fa-eye m-r-5 icon3 view-followup" data-id="${data?.id}" title="View Followup"></i>
                @endif 
                @if (app('hasPermission')(2, 'update') && !$isPatientRole) 
                    <i class="fa fa-pencil m-r-5 icon1 edit-followup" data-id="${data?.id}" title="Edit Followup"></i> 
                @endif
                @if (app('hasPermission')(2, 'delete') && !$isPatientRole) 
                    <i class="fa fa-trash-o m-r-5 icon2 delete-followup" data-id="${data?.id}" title="Delete Followup"></i> 
                @endif
                @if (app('hasPermission')(2, 'view')) 
                    <i class="fa fa-download m-r-5 icon4 download-followup" data-id="${data?.id}" title="Download Followup PDF"></i>
                @endif
            </div>
        `;

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="3">
                    <div class="details-content">
                        @if($currentProjectTypeId !== 3)
                        <div><strong>Treatment:</strong> ${treatment}</div>
                        @endif
                        <div><strong>Followup Date:</strong> ${followupDate}</div>
                        <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });

    $(window).on('resize', function() {
        if ($(window).width() >= 768) {
            $('.details-row').remove();
            $('.expand-btn i')
                .removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');
        }
    });
</script>
@endsection




