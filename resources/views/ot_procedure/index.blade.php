@extends('layout.app')
<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }
    .swal-cancel-btn {
        color: white !important;
    }
    .details-row {
        background-color: #f8f9fa;
    }
    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }
      .card-header{
        background-color:#f89884 !important; 
    }
    .btn-rounded{
        background-color: #fed9cf !important;
    }
    button.expand-btn {
        background: #f89884;
        color: white;
        padding: 6px 8px;
        border-radius: 8px;
        border: none;
    }
    span.btn.btn-scheduled.btn-rounded {
    color: white;
    background: #f89884 !important;
}
span.btn.btn-completed.btn-rounded {
    background: #cfece0 !important;
}
    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;
        }
        .d-none.d-md-table-cell {
            display: none !important;
        }
    }
    @media (min-width: 768px) {
        .d-table-cell.d-md-none {
            display: none !important;
        }
        .details-row {
            display: none !important;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-stethoscope px-2" style="font-size:20px"></i> All OT Procedure
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(24, 'create'))
                                <a href="{{ route('ot.create') }}" class="btn btn-rounded btn-hdr">
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
                                            <th class="d-none d-md-table-cell">Procedure Name</th>
                                            <th class="d-none d-md-table-cell">Procedure Date</th>
                                            <th class="d-none d-md-table-cell">Status</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="servicesTableBody"></tbody>
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
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                Swal.fire("Error", "Branch ID not found in localStorage!", "error");
                return;
            }
            window.location.href = "{{ route('ot.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');
            let token = localStorage.getItem("authToken");
            let otTable;

            function ucfirst(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
            }

            function initOtTable() {
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
                    otTable = $('#servicetbl').DataTable({
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
                                url: '/api/all-ot-procedures',
                                type: 'GET',
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                headers: { "Authorization": "Bearer " + token },
                                success: function (response) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || 0,
                                        data: response.data || []
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
                                    let defaultImage = "{{ asset('admin/assets/img/img1.png') }}";
                                    let patientImage = row.patient_image ? row.patient_image : defaultImage;
                                    return `
                                        <div class="view-ot" data-id="${row.id}" style="cursor:pointer; display:flex; align-items:center;">
                                            <img src="${patientImage}" width="40" height="40"
                                                style="border-radius: 50%; object-fit: cover; margin-right: 10px;">
                                            <span>${ucfirst(row.patient_name)}</span>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: "doctor_name",
                                render: function (data) { return ucfirst(data); }
                            },
                            {
                                data: "procedure_name",
                                render: function (data) { return data ?? 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "procedure_date",
                                render: function (data) { return data ?? 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "status",
                                render: function (data) {
                                    return data === 'completed'
                                        ? '<span class="btn btn-completed btn-rounded">Completed</span>'
                                        : '<span class="btn btn-scheduled btn-rounded">Scheduled</span>';
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return `
                                        <div class="icon" style="cursor:pointer">
                                            @if (app('hasPermission')(24, 'view'))
                                                <i class="fa fa-eye m-r-5 icon3 view-ot" data-id="${row.id}"></i>
                                            @endif
                                            @if (app('hasPermission')(24, 'update'))
                                                <i class="fa fa-pencil m-r-5 icon1 edit-ot" data-id="${row.id}"></i>
                                            @endif
                                            @if (app('hasPermission')(24, 'delete'))
                                                <i class="fa fa-trash-o m-r-5 icon2 delete-ot" data-id="${row.id}"></i>
                                            @endif
                                        </div>
                                    `;
                                },
                                className: "d-none d-md-table-cell",
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
                        ]
                    });
                }, 50);
            }

            initOtTable();

            // Expand/collapse logic for mobile
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#servicetbl')) {
                    return;
                }

                let table = $('#servicetbl').DataTable();
                let row = table.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let procedureName = data.procedure_name ?? 'N/A';
                let procedureDate = data.procedure_date ?? 'N/A';
                let status = data.status === 'completed'
                    ? '<span class="btn btn-success btn-rounded btn-hdr">Completed</span>'
                    : '<span class="btn btn-warning btn-rounded btn-hdr">Scheduled</span>';
                let actions = `
                    <div class="icon" style="cursor:pointer">
                        @if (app('hasPermission')(24, 'view'))
                            <i class="fa fa-eye m-r-5 icon3 view-ot" data-id="${data.id}"></i>
                        @endif
                        @if (app('hasPermission')(24, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-ot" data-id="${data.id}"></i>
                        @endif
                        @if (app('hasPermission')(24, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-ot" data-id="${data.id}"></i>
                        @endif
                    </div>
                `;

                let detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="6">
                            <div class="details-content">
                                <div><strong>Procedure Name:</strong> ${procedureName}</div>
                                <div><strong>Procedure Date:</strong> ${procedureDate}</div>
                                <div><strong>Status:</strong> ${status}</div>
                                <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            // Delete handler
            $(document).on('click', '.delete-ot', function() {
                var otId = $(this).data('id');
                if (!otId) {
                    Swal.fire('Error', 'OT ID is missing!', 'error');
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
                            url: '/api/delete-ot/' + otId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'OT deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Failed to delete OT.', 'error');
                            }
                        });
                    }
                });
            });

            // View + Edit
            $(document).on('click', '.view-ot', function() {
                var otId = $(this).data('id');
                window.location.href = '/ot/display/' + otId;
            });

            $(document).on('click', '.edit-ot', function() {
                var otId = $(this).data('id');
                window.location.href = '/ot/edit/' + otId;
            });
        });
    </script>
@endsection
