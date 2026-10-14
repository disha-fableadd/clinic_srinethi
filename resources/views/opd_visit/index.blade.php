@extends('layout.app')

<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }
span.btn.btn-completed.btn-rounded {
    background-color: #cfece0 !important;
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

    #servicetbl thead th {
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

    @media screen and (max-width:767px) {

        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;

        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">
               
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-stethoscope px-2" style="font-size:20px"></i> All OPD
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(23, 'create'))
                                <a href="{{ route('opd_visit.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="servicetbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>

                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th class="d-none d-md-table-cell">Date</th>
                                            <th class="d-none d-md-table-cell">Status</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                             <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="servicesTableBody">
                                        <!-- Data will be loaded dynamically here -->
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

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
            let branchId = localStorage.getItem('selectedBranchId');
            let token = localStorage.getItem("authToken");

             $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');

            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }

            // Redirect to export route with branch_id
            window.location.href = "{{ route('opd_visit.export') }}" + "?branch_id=" + branchId;
        });

            function renderStatusBadge(status) {
                if (status === 'active') {
                    return '<span class="btn btn-primary btn-rounded btn-hdr"><i class="fas fa-play-circle"></i> Active</span>';
                }
                if (status === 'completed') {
                    return '<span class="btn btn-completed btn-rounded"><i class="fas fa-check-circle"></i> Complete</span>';
                }
                return '<span class="btn btn-danger btn-rounded btn-hdr"><i class="fas fa-question-circle"></i> Unknown</span>';
            }

            function renderPatientCell(visit) {
                let defaultImage = "{{ asset('admin/assets/img/img1.png') }}";
                let patientImage = visit?.patient?.profile ?? defaultImage;
                let patientName = visit?.patient ? ucfirst(visit.patient.fullname) : 'N/A';
                return `
                    <div class="view-opd" data-id="${visit.id}" style="cursor:pointer; display:flex; align-items:center;">
                        <img src="${patientImage}" width="40" height="40" style="border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        <span>${patientName}</span>
                    </div>
                `;
            }

            function renderDoctorCell(visit) {
                let doctorName = visit?.doctor ? ucfirst(visit.doctor.fullname) : 'N/A';
                return `<span class="view-opd" data-id="${visit.id}" style="cursor:pointer;">${doctorName}</span>`;
            }

            function renderVisitDate(visit) {
                return visit?.visit_date ? new Date(visit.visit_date).toLocaleDateString('en-CA') : 'N/A';
            }

            function renderActions(visit) {
                return `
                    <div class="icon" style="cursor:pointer">
                        @if (app('hasPermission')(23, 'view'))
                            <i class="fa fa-eye m-r-5 icon3 view-opd" data-id="${visit.id}" title="View"></i>
                        @endif
                        @if (app('hasPermission')(23, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-opd" data-id="${visit.id}" title="Edit"></i>
                        @endif
                        @if (app('hasPermission')(23, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-opd" data-id="${visit.id}" title="Delete"></i>
                        @endif
                        @if (app('hasPermission')(23, 'view'))
                            <i class="fa fa-download m-r-5 icon4 download-opd" data-id="${visit.id}" title="Download OPD PDF"></i>
                        @endif
                    </div>
                `;
            }

            function initOpdTable() {
                if ($.fn.DataTable.isDataTable("#servicetbl")) {
                    try {
                        let existingTable = $('#servicetbl').DataTable();
                        existingTable.destroy();
                        $('#servicetbl').removeClass('dataTable');
                        $('#servicetbl tbody').empty();
                        $.removeData($('#servicetbl')[0], 'DataTable');
                        $.removeData($('#servicetbl')[0], 'DataTables_DataTable');
                        $('#servicetbl').off();
                    } catch (e) {
                        $('#servicetbl tbody').empty();
                        $.removeData($('#servicetbl')[0]);
                    }
                }

                if ($('#servicetbl tbody').length === 0) {
                    $('#servicetbl').append('<tbody id="servicesTableBody"></tbody>');
                }

                setTimeout(function () {
                    $('#servicetbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        responsive: false,
                        ajax: function (data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const perPage = data.length;
                            $.ajax({
                                url: '/api/all-opd-visits',
                                type: 'GET',
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                headers: { "Authorization": "Bearer " + token },
                                success: function (response) {
                                    const rows = response.data || response.opd_visits || [];
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || rows.length || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || rows.length || 0,
                                        data: rows
                                    });
                                },
                                error: function (xhr) {
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
                                data: null,
                                render: function (data, type, row) {
                                    return renderPatientCell(row);
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return renderDoctorCell(row);
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `<span class="view-opd" data-id="${row.id}" style="cursor:pointer;">${renderVisitDate(row)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "status",
                                render: function (data) {
                                    return renderStatusBadge(data);
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return renderActions(row);
                                },
                                className: "d-none d-md-table-cell",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="btn btn-link expand-btn"><i class="fa fa-chevron-down"></i></button>
                                    `;
                                },
                                className: "d-table-cell d-md-none text-center",
                                orderable: false
                            }
                        ]
                    });
                }, 50);
            }

            initOpdTable();


            $(document).on('click', '.download-opd', function() {
                const id = $(this).data('id');
                window.location.href = `/api/opds/${id}/download`;
            });

            // Function to delete a service
            $(document).on('click', '.delete-opd', function() {
                var opdId = $(this).data('id');

                if (!opdId) {
                    Swal.fire('Error', 'opdId ID is missing!', 'error');
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
                            url: '/api/delete-opd-visit/' + opdId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'OPD deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location
                                        .reload(); // Reload the page to update the list
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Failed to delete opd.', 'error');
                            }
                        });
                    }
                });
            });


              // === Mobile Dropdown Expand ===
    $(document).on("click", ".expand-btn", function (e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");

        if (!$.fn.DataTable.isDataTable('#servicetbl')) {
            return;
        }

        const table = $('#servicetbl').DataTable();
        const row = table.row(tr);
        const data = row.data();
        if (!data) return;

        const existingRow = tr.next(".details-row");
        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        const visitDate = renderVisitDate(data);
        const statusHtml = renderStatusBadge(data.status);
        const actionsHtml = renderActions(data);

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="3">
                    <div class="details-content">
                        <div class="mb-2"><strong>Date:</strong> ${visitDate}</div>
                        <div class="mb-2 pt-2"><strong>Status:</strong> ${statusHtml}</div>
                        <div class="mt-2"><strong>Actions:</strong> ${actionsHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });
            $(document).on('click', '.view-opd', function() {
                var opdId = $(this).data('id');
                window.location.href = '/opd/display/' + opdId;
            });

            $(document).on('click', '.edit-opd', function() {
                var opdId = $(this).data('id');
                window.location.href = '/opd/edit/' + opdId;
            });
        });
    </script>
@endsection
