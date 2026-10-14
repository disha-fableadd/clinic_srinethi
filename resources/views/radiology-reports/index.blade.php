@extends('layout.app')

<style>
    .download {
        padding: 10px !important;
        margin-left: 5px !important;
    }

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    .filess {
        padding: 10px 12px !important;
    }
      .card-header{
        background-color:#f89884 !important; 
    }
    .btn-rounded{
        background-color: #fed9cf !important;
    }

    @media screen and (max-width:767px) {
        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;
        }
    }
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
colgroup {
        display: none;
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
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-flask px-2" style="font-size:20px"></i> All Radiology Reports
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton"> <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span></button>
                            @if (app('hasPermission')(22, 'create'))
                                <a href="{{ route('radiology-reports.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="radiologytesttbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
    <tr>
        <th>Patient Name</th>
        <th>Test Name</th>
        <th class="d-none d-md-table-cell">Report Date</th>
        <th class="d-none d-md-table-cell">Report File</th>
        <th class="d-none d-md-table-cell">Action</th>
        <th class="d-table-cell d-md-none">Details</th>
    </tr>
</thead>

                                    <tbody id="radiologyTestsTableBody">

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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    
     <script>
        let branchId = localStorage.getItem('selectedBranchId');
        $(document).ready(function() {
            let token = localStorage.getItem("authToken");

            function ucfirst(str) {
                if (!str) return '';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function renderPatientCell(report) {
                let defaultImage = "{{ asset('admin/assets/img/img1.png') }}";
                let patientImage = report.patient?.profile ? report.patient.profile : defaultImage;
                let patientName = report.patient ? ucfirst(report.patient.fullname) : 'N/A';
                return `
                    <div class="view-reports" data-id="${report.id}" style="cursor:pointer; display:flex; align-items:center;">
                        <img src="${patientImage}" width="40" height="40" style="border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        <span>${patientName}</span>
                    </div>
                `;
            }

            function renderReportFile(report) {
                const reportFile = report.converted_image;
                if (reportFile) {
                    const fileUrl = '/public/' + reportFile.replace(/^\/+/, '');
                    return `
                        <a href="${fileUrl}" target="_blank" class="btn btn-primary btn-sm filess"><i class="fas fa-file-alt"></i></a>
                        <a href="${fileUrl}" download class="btn btn-primary btn-sm download"><i class="fas fa-download"></i></a>
                    `;
                }
                return 'No file';
            }

            function renderActions(report) {
                return `
                    <div class="icon" style="cursor:pointer">
                        <i class="fa fa-eye m-r-5 icon3 view-reports" data-id="${report.id}" style="cursor:pointer"></i>
                        <i class="fa fa-pencil m-r-5 icon1 edit-reports" data-id="${report.id}" style="cursor:pointer"></i>
                        <i class="fa fa-trash-o m-r-5 icon2 delete-reports" data-id="${report.id}" style="cursor:pointer"></i>
                    </div>
                `;
            }

            function initRadiologyReportsTable() {
                if ($.fn.DataTable.isDataTable("#radiologytesttbl")) {
                    try {
                        let existingTable = $('#radiologytesttbl').DataTable();
                        existingTable.destroy();
                        $('#radiologytesttbl').removeClass('dataTable');
                        $('#radiologytesttbl tbody').empty();
                        $.removeData($('#radiologytesttbl')[0], 'DataTable');
                        $.removeData($('#radiologytesttbl')[0], 'DataTables_DataTable');
                        $('#radiologytesttbl').off();
                    } catch (e) {
                        $('#radiologytesttbl tbody').empty();
                        $.removeData($('#radiologytesttbl')[0]);
                    }
                }

                if ($('#radiologytesttbl tbody').length === 0) {
                    $('#radiologytesttbl').append('<tbody id="radiologyTestsTableBody"></tbody>');
                }

                setTimeout(function () {
                    $('#radiologytesttbl').DataTable({
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
                                url: '/api/radiology-reports',
                                type: 'GET',
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                dataType: 'json',
                                headers: { "Authorization": "Bearer " + token },
                                success: function(response) {
                                    const rows = response.radiology_report || response.data || [];
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || rows.length || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || rows.length || 0,
                                        data: rows
                                    });
                                },
                                error: function(xhr) {
                                    console.log(xhr.responseText);
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
                                    return row.test?.test_name || 'N/A';
                                }
                            },
                            {
                                data: "report_date",
                                render: function (data) { return data || 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: null,
                                render: function (data, type, row) { return renderReportFile(row); },
                                className: "d-none d-md-table-cell report-icon"
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
                                        <button class="expand-btn">
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

            initRadiologyReportsTable();

            // Expand/collapse logic for mobile
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#radiologytesttbl')) {
                    return;
                }

                let table = $('#radiologytesttbl').DataTable();
                let row = table.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let reportDate = data.report_date || 'N/A';
                let reportFile = renderReportFile(data);
                let actions = renderActions(data);

                let detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="5">
                            <div class="details-content">
                                <div><strong>Report Date:</strong> ${reportDate}</div>
                                <div class="mt-2"><strong>Report File:</strong> ${reportFile}</div>
                                <div class="mt-2"><strong>Actions:</strong> ${actions}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            // Delete radiology test
            $(document).on('click', '.delete-reports', function() {
                var testId = $(this).data('id');
                if (!testId) {
                    Swal.fire('Error', 'Test ID is missing!', 'error');
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this radiology test?",
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
                            url: '/api/radiology-reports/' + testId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Deleted!', 'Radiology test deleted successfully!', 'success')
                                    .then(() => {
                                        if ($.fn.DataTable.isDataTable("#radiologytesttbl")) {
                                            $('#radiologytesttbl').DataTable().ajax.reload(null, false);
                                        } else {
                                            location.reload();
                                        }
                                    });
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Failed to delete radiology test.', 'error');
                            }
                        });
                    }
                });
            });

            // View radiology test
            $(document).on('click', '.view-reports', function() {
                var testId = $(this).data('id');
                window.location.href = '/radiology-reports/' + testId;
            });

            // Edit radiology test
            $(document).on('click', '.edit-reports', function() {
                var testId = $(this).data('id');
                window.location.href = '/radiology-reports/edit/' + testId;
            });

            // Export button
            $(document).on('click', '#exportButton', function() {
                let branchId = localStorage.getItem('selectedBranchId');
                if (!branchId) {
                    alert("Please select a branch first.");
                    return;
                }
                window.location.href = "{{ route('radio-report.export') }}" + "?branch_id=" + branchId;
            });
        });
    </script>
@endsection
