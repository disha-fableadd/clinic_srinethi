@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/assign-therapy-index.css') }}">

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px"></div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-flask px-2" style="font-size:20px"></i> Assigned Therapy List
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(27, 'create'))
                                <a href="{{ route('assign-therapy.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="assign-therapytTbl" class="table custom-table">
                                    <thead class="text-center" style="background-color:#ff8e6;">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Therapy</th>
                                            <th class="d-none d-md-table-cell">Doctor</th>
                                            <th class="d-none d-md-table-cell">Start Date</th>
                                            <th class="d-none d-md-table-cell">End Date</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- Mobile-only column -->
                                        </tr>
                                    </thead>

                                    <tbody class="reportBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('assign-therapy.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function() {
            fetchReports();
        });

        let reportTable;

        function fetchReports() {
            let branchId = localStorage.getItem('selectedBranchId');

            if ($.fn.DataTable.isDataTable("#assign-therapytTbl")) {
                try {
                    let existingTable = $('#assign-therapytTbl').DataTable();
                    existingTable.destroy();
                    $('#assign-therapytTbl').removeClass('dataTable');
                    $('#assign-therapytTbl tbody').empty();
                    $.removeData($('#assign-therapytTbl')[0], 'DataTable');
                    $.removeData($('#assign-therapytTbl')[0], 'DataTables_DataTable');
                    $('#assign-therapytTbl').off();
                } catch (e) {
                    $('#assign-therapytTbl tbody').empty();
                    $.removeData($('#assign-therapytTbl')[0]);
                }
            }

            if ($('#assign-therapytTbl tbody').length === 0) {
                $('#assign-therapytTbl').append('<tbody class="reportBody"></tbody>');
            }

            setTimeout(function() {
                reportTable = $('#assign-therapytTbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: "/api/assigned-therapies",
                            type: "GET",
                            dataType: "json",
                            data: {
                                branch_id: branchId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            success: function(json) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: json.assigned_therapies || []
                                });
                            },
                            error: function(xhr) {
                                console.error("Failed to load assigned therapies", xhr.responseText);
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                                Swal.fire('Error', 'Failed to load assigned therapy data', 'error');
                            }
                        });
                    },
                    columns: [{
                            data: 'patient',
                            render: function(data, type, row) {
                                const patient = data || {};
                                const profileImage = patient.profile ?? '/default.png';
                                return `
                                    <img src="${profileImage}" class="rounded-circle" width="40" height="40"> ${patient.fullname ?? 'N/A'}
                                `;
                            },
                            className: "view-report",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'therapy',
                            render: function(data) {
                                const therapy = data || {};
                                return `${therapy.name ?? 'N/A'}`;
                            },
                            className: "view-report"
                        },
                        {
                            data: 'doctor',
                            render: function(data) {
                                const doctor = data || {};
                                return `${doctor.fullname ?? 'N/A'}`;
                            },
                            className: "d-none d-md-table-cell view-report"
                        },
                        {
                            data: 'start_date',
                            render: data => data ?? 'N/A',
                            className: "d-none d-md-table-cell view-report"
                        },
                        {
                            data: 'end_date',
                            render: data => data ?? 'N/A',
                            className: "d-none d-md-table-cell view-report"
                        },
                        {
                            data: 'id',
                            render: function(data) {
                                return `
                                    <div class="icon">
                                        @if (app('hasPermission')(27, 'view'))
                                            <i class="fa fa-eye m-r-5 icon3 view-report" style="cursor:pointer" data-id="${data}"></i>
                                        @endif
                                        @if (app('hasPermission')(27, 'update'))
                                            <i class="fa fa-pencil m-r-5 icon1 edit-report" style="cursor:pointer" data-id="${data}"></i>
                                        @endif
                                        @if (app('hasPermission')(27, 'delete'))
                                            <i class="fa fa-trash m-r-5 icon2 delete-report" style="cursor:pointer" data-id="${data}"></i>
                                        @endif
                                        @if (app('hasPermission')(27, 'view'))
                                            <i class="fa fa-download m-r-5 icon4 download-assigned-therapy" data-id="${data}" title="Download Therapy PDF"></i>
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
                                    <button class="btn btn-link expand-btn">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
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
                        error: function(xhr, error, code) {
                            return "Unable to load data. Please try again.";
                        }
                    }
                });
            }, 50);
        }

        // Handle expandable rows for mobile
        $(document).on('click', '.expand-btn', function() {
            const btn = $(this);
            const icon = btn.find('i');
            const tr = btn.closest('tr');
            const existingRow = tr.next('.details-row');

            if (existingRow.length) {
                existingRow.slideToggle(300);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
                return;
            }

            let row = reportTable.row(tr);
            let data = row.data();

            const doctor = data?.doctor?.fullname ?? 'N/A';
            const startDate = data?.start_date ?? 'N/A';
            const endDate = data?.end_date ?? 'N/A';
            const actionHtml = `
                <div class="icon">
                    @if (app('hasPermission')(27, 'view'))
                        <i class="fa fa-eye m-r-5 icon3 view-report" style="cursor:pointer" data-id="${data?.id}"></i>
                    @endif
                    @if (app('hasPermission')(27, 'update'))
                        <i class="fa fa-pencil m-r-5 icon1 edit-report" style="cursor:pointer" data-id="${data?.id}"></i>
                    @endif
                    @if (app('hasPermission')(27, 'delete'))
                        <i class="fa fa-trash m-r-5 icon2 delete-report" style="cursor:pointer" data-id="${data?.id}"></i>
                    @endif
                    @if (app('hasPermission')(27, 'view'))
                        <i class="fa fa-download m-r-5 icon4 download-assigned-therapy" data-id="${data?.id}" title="Download Therapy PDF"></i>
                    @endif
                </div>
            `;

            const detailsRow = $(`
                <tr class="details-row">
                    <td colspan="3">
                        <div class="details-content">
                            <div><strong>Doctor:</strong> ${doctor}</div>
                            <div><strong>Start Date:</strong> ${startDate}</div>
                            <div><strong>End Date:</strong> ${endDate}</div>
                            <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                        </div>
                    </td>
                </tr>
            `);

            tr.after(detailsRow);
            icon.toggleClass('fa-chevron-down fa-chevron-up');
        });

        $(window).on('resize', function() {
            if ($(window).width() >= 768) {
                $('.details-row').remove();
                $('.expand-btn i')
                    .removeClass('fa-chevron-up')
                    .addClass('fa-chevron-down');
            }
        });

        $(document).on('click', '.view-report', function() {
            const id = $(this).data('id');
            window.location.href = `/assign-therapy/show/${id}`;
        });

        $(document).on('click', '.edit-report', function() {
            const id = $(this).data('id');
            window.location.href = `/assign-therapy/edit/${id}`;
        });

        $(document).on('click', '.download-assigned-therapy', function() {
            const id = $(this).data('id');
            window.location.href = `/api/assigned-therapies/${id}/download`;
        });

        $(document).on('click', '.delete-report', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this test?",
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
                        url: `/api/assigned-therapies/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            fetchReports();
                        },
                        error: function() {
                            Swal.fire('Error', 'Could not delete the report.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection

