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

    .details-row {
        background-color: #f8f9fa;
        display: none;
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
        cursor: pointer;
    }

    button.btn.btn-link.expand-btn {
        background: #f89884;
        color: white;
        padding: 7px;
        border-radius: 9px;
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
@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row" style="padding-top:15px">

            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" >
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-flask px-2" style="font-size:20px"></i> Pathology Report List
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(20, 'create'))
                                <a href="{{ route('pathology_reports.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="pathologyReportTbl" class="table custom-table">
                                    <thead class="text-center" style="background-color:#ff8e6;">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Test</th>
                                            <th class="d-none d-md-table-cell">Sample Collected</th>
                                            <th class="d-none d-md-table-cell">Report Date</th>
                                            <th class="d-none d-md-table-cell">Result</th>
                                            <th class="d-none d-md-table-cell">Report File</th>
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

    {{-- DataTables CDN --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

  

    <script>
        $('#exportButton').on('click', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('pathology-reports.export') }}" + "?branch_id=" + branchId;
        });

        const IMAGE_PATH = "{{ env('IMAGE_PATH') }}";
        let branchId = localStorage.getItem('selectedBranchId');

        $(document).ready(function() {
            let token = localStorage.getItem("authToken");
            initReportsTable();

            function renderReportFile(report) {
                const basePath = IMAGE_PATH;
                const reportFile = report.report_file;
                const fullPath = reportFile ? `${basePath}/${reportFile}` : '';
                if (!fullPath) {
                    return 'No file';
                }
                return `
                    <a href="${fullPath}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-file-alt"></i>
                    </a>
                    <a href="${fullPath}" download class="btn btn-primary btn-sm download">
                        <i class="fas fa-download"></i>
                    </a>
                `;
            }

            function renderActions(report) {
                return `
                    <div class="icon">
                        @if (app('hasPermission')(20, 'view'))
                            <i class="fa fa-eye m-r-5 icon3 view-report" style="cursor:pointer" data-id="${report.id}"></i>
                        @endif
                        @if (app('hasPermission')(20, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-report" style="cursor:pointer" data-id="${report.id}"></i>
                        @endif
                        @if (app('hasPermission')(20, 'delete'))
                            <i class="fa fa-trash m-r-5 icon2 delete-report" style="cursor:pointer" data-id="${report.id}"></i>
                        @endif
                    </div>
                `;
            }

            function initReportsTable() {
                if ($.fn.DataTable.isDataTable("#pathologyReportTbl")) {
                    try {
                        let existingTable = $('#pathologyReportTbl').DataTable();
                        existingTable.destroy();
                        $('#pathologyReportTbl').removeClass('dataTable');
                        $('#pathologyReportTbl tbody').empty();
                        $.removeData($('#pathologyReportTbl')[0], 'DataTable');
                        $.removeData($('#pathologyReportTbl')[0], 'DataTables_DataTable');
                        $('#pathologyReportTbl').off();
                    } catch (e) {
                        $('#pathologyReportTbl tbody').empty();
                        $.removeData($('#pathologyReportTbl')[0]);
                    }
                }

                if ($('#pathologyReportTbl tbody').length === 0) {
                    $('#pathologyReportTbl').append('<tbody class="reportBody"></tbody>');
                }

                setTimeout(function () {
                    $('#pathologyReportTbl').DataTable({
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
                                url: "/api/pathology-reports",
                                type: "GET",
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                dataType: "json",
                                headers: { "Authorization": "Bearer " + token },
                                success: function(response) {
                                    const rows = response.pathology_report || response.data || response || [];
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || rows.length || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || rows.length || 0,
                                        data: rows
                                    });
                                },
                                error: function(xhr) {
                                    console.error("Failed to load pathology reports", xhr.responseText);
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
                                    const patient = row.patient ?? {};
                                    const profileImage = patient.profile ?? '/default.png';
                                    return `
                                        <div class="view-report" style="cursor:pointer" data-id="${row.id}">
                                            <img src="${profileImage}" class="rounded-circle" width="40" height="40"> ${patient.fullname ?? 'N/A'}
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return row.test?.test_name ?? 'N/A';
                                }
                            },
                            {
                                data: "sample_collected_date",
                                render: function (data) { return data || 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "report_date",
                                render: function (data) { return data || 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "result",
                                render: function (data) { return data || 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: null,
                                render: function (data, type, row) { return renderReportFile(row); },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "id",
                                render: function (data, type, row) { return renderActions(row); },
                                className: "d-none d-md-table-cell",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="btn btn-link expand-btn">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                className: "d-table-cell d-md-none",
                                orderable: false
                            }
                        ]
                    });
                }, 50);
            }

            // Handle expandable rows for mobile
            $(document).on('click', '.expand-btn', function() {
                const btn = $(this);
                const icon = btn.find('i');
                const tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#pathologyReportTbl')) {
                    return;
                }

                const table = $('#pathologyReportTbl').DataTable();
                const row = table.row(tr);
                const data = row.data();
                if (!data) return;

                const existingRow = tr.next('.details-row');
                if (existingRow.length) {
                    existingRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                const sampleCollected = data.sample_collected_date || 'N/A';
                const reportDate = data.report_date || 'N/A';
                const result = data.result || 'N/A';
                const reportFile = renderReportFile(data);
                const actionHtml = renderActions(data);

                const detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="3">
                            <div class="details-content">
                                <div><strong>Sample Collected:</strong> ${sampleCollected}</div>
                                <div><strong>Report Date:</strong> ${reportDate}</div>
                                <div><strong>Result:</strong> ${result}</div>
                                <div><strong>Report File:</strong> ${reportFile}</div>
                                <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });

            $(document).on('click', '.view-report', function() {
                const id = $(this).data('id');
                window.location.href = `/pathology_reports/show/${id}`;
            });

            $(document).on('click', '.edit-report', function() {
                const id = $(this).data('id');
                window.location.href = `/pathology_reports/edit/${id}`;
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
                            url: '/api/pathology-reports/' + id,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success');
                                if ($.fn.DataTable.isDataTable("#pathologyReportTbl")) {
                                    $('#pathologyReportTbl').DataTable().ajax.reload(null, false);
                                } else {
                                    fetchReports();
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Could not delete the report.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
