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
                        <div class="card-header" >
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-flask px-2" style="font-size:20px"></i> All Radiology Tests
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton"> 
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span></button>
                            @if (app('hasPermission')(21, 'create'))
                                <a href="{{ route('radiology-tests.create') }}" class="btn btn-rounded btn-hdr">
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
                                            <th >Test Name</th>
                                            <th >Test Code</th>
                                            <th class="d-none d-md-table-cell">Body Part</th>
                                            <th class="d-none d-md-table-cell">Cost</th>
                                            <th class="d-none d-md-table-cell">Report Format</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- Mobile-only column -->
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

    {{-- <script>
        const formatLabels = {
            'dcm': 'DICOM (.dcm)',
            'jpg': 'JPEG (.jpg)',
            'jpeg': 'JPEG (.jpeg)',
            'png': 'PNG (.png)',
            'tiff': 'TIFF (.tiff)',
            'bmp': 'Bitmap (.bmp)',
            'pdf': 'PDF (.pdf)',
            'doc': 'Word (.doc)',
            'docx': 'Word (.docx)',
            'webp': 'WEBP (.webp)',
            'avi': 'Video (.avi)',
            'mp4': 'Video (.mp4)',
            'zip': 'ZIP (.zip)'
        };

        let branchId = localStorage.getItem('selectedBranchId');
        $(document).ready(function() {
            // Fetch radiology tests from API
            $.ajax({
                url: '/api/radiology-tests',
                type: 'GET',
                data: {
                    branch_id: branchId
                },
                dataType: 'json',
                success: function(data) {
                    var tests = data.radiology;

                    // Reinitialize DataTable
                    if ($.fn.DataTable.isDataTable("#radiologytesttbl")) {
                        $('#radiologytesttbl').DataTable().destroy();
                    }
                    var tableBody = $('#radiologyTestsTableBody');
                    tableBody.empty();
                    tests.forEach(function(test) {
                        let formatLabel = formatLabels[test.report_format] || 'N/A';
                        var row = '<tr>';
                        row += '<td style="cursor:pointer" class="view-test" data-id="' + test
                            .id + '">' + (test.test_name || 'N/A') + '</td>';
                        row += '<td>' + (test.test_code || 'N/A') + '</td>';
                        row += '<td>' + (test.body_part || 'N/A') + '</td>';
                        row += '<td>' + (test.cost !== null ? test.cost : 'N/A') + '</td>';
                        row += '<td>' + formatLabel + '</td>';
                        // // Report Format column
                        // if (test.report_format) {
                        //     var fileUrl = '/' + test.report_format;
                        //     row += '<td class="report-icon">' +
                        //         '<a href="' + fileUrl + '" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-file-alt"></i></a>' +
                        //         '<a href="' + fileUrl + '" download class="btn btn-primary btn-sm download"><i class="fas fa-download"></i></a>' +
                        //         '</td>';
                        // } else {
                        //     row += '<td>No file</td>';
                        // }
                        row += '<td>';
                        row += '   <div class="icon" style="cursor:pointer">';
                        row += '   <i class="fa fa-eye m-r-5 icon3 view-test" data-id="' + test
                            .id + '" style="cursor:pointer"></i>';
                        row += '   <i class="fa fa-pencil m-r-5 icon1 edit-test" data-id="' +
                            test.id + '" style="cursor:pointer"></i>';
                        row += '   <i class="fa fa-trash-o m-r-5 icon2 delete-test" data-id="' +
                            test.id + '" style="cursor:pointer"></i>';
                        row += '</div';
                        row += '</td>';
                        row += '</tr>';
                        tableBody.append(row);
                    });
                    $('#radiologytesttbl').DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        destroy: true
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });

            // Delete radiology test
            $(document).on('click', '.delete-test', function() {
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
                            url: '/api/radiology-tests/' + testId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Deleted!',
                                        'Radiology test deleted successfully!',
                                        'success')
                                    .then(() => {
                                        location.reload();
                                    });
                            },
                            error: function(xhr) {
                                let errorMsg = 'Failed to delete test.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }

                        });
                    }
                });
            });


            // View radiology test
            $(document).on('click', '.view-test', function() {
                var testId = $(this).data('id');
                window.location.href = '/radiology-tests/' + testId;
            });

            // Edit radiology test
            $(document).on('click', '.edit-test', function() {
                var testId = $(this).data('id');
                window.location.href = '/radiology-tests/edit/' + testId;
            });

            // $(document).on('click', '#exportButton', function() {
            //     window.location.href = "{{ route('radio-test.export') }}";
            // });



            $(document).on('click', '#exportButton', function() {
                let branchId = localStorage.getItem('selectedBranchId');

                if (!branchId) {
                    alert("Please select a branch first.");
                    return;
                }

                window.location.href = "{{ route('radio-test.export') }}" + "?branch_id=" + branchId;
            });

        });
    </script> --}}

    <script>
        const formatLabels = {
            'dcm': 'DICOM (.dcm)',
            'jpg': 'JPEG (.jpg)',
            'jpeg': 'JPEG (.jpeg)',
            'png': 'PNG (.png)',
            'tiff': 'TIFF (.tiff)',
            'bmp': 'Bitmap (.bmp)',
            'pdf': 'PDF (.pdf)',
            'doc': 'Word (.doc)',
            'docx': 'Word (.docx)',
            'webp': 'WEBP (.webp)',
            'avi': 'Video (.avi)',
            'mp4': 'Video (.mp4)',
            'zip': 'ZIP (.zip)'
        };
        let branchId = localStorage.getItem('selectedBranchId');

        $(document).ready(function() {
            let token = localStorage.getItem("authToken");

            function renderActions(test) {
                return `
                    <div class="icon" style="cursor:pointer">
                        <i class="fa fa-eye m-r-5 icon3 view-test" data-id="${test.id}" style="cursor:pointer"></i>
                        <i class="fa fa-pencil m-r-5 icon1 edit-test" data-id="${test.id}" style="cursor:pointer"></i>
                        <i class="fa fa-trash-o m-r-5 icon2 delete-test" data-id="${test.id}" style="cursor:pointer"></i>
                    </div>
                `;
            }

            function renderFormat(test) {
                return formatLabels[test.report_format] || 'N/A';
            }

            function initRadiologyTable() {
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
                                url: '/api/radiology-tests',
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
                                    const rows = response.radiology || response.data || [];
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
                                data: "test_name",
                                render: function (data, type, row) {
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${data || 'N/A'}</span>`;
                                }
                            },
                            {
                                data: "test_code",
                                render: function (data) { return data || 'N/A'; }
                            },
                            {
                                data: "body_part",
                                render: function (data) { return data || 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "cost",
                                render: function (data) { return data !== null ? data : 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "report_format",
                                render: function (data, type, row) { return renderFormat(row); },
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
                                className: "d-table-cell d-md-none text-center",
                                orderable: false
                            }
                        ]
                    });
                }, 50);
            }

            initRadiologyTable();

            // Handle expandable rows for mobile
            $(document).on('click', '.expand-btn', function() {
                const btn = $(this);
                const icon = btn.find('i');
                const tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#radiologytesttbl')) {
                    return;
                }

                const table = $('#radiologytesttbl').DataTable();
                const row = table.row(tr);
                const data = row.data();
                if (!data) return;

                const existingRow = tr.next('.details-row');
                if (existingRow.length) {
                    existingRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                const bodyPart = data.body_part || 'N/A';
                const cost = data.cost !== null ? data.cost : 'N/A';
                const reportFormat = renderFormat(data);
                const actionHtml = renderActions(data);

                const detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="3">
                            <div class="details-content">
                                <div><strong>Body Part:</strong> ${bodyPart}</div>
                                <div><strong>Cost:</strong> ${cost}</div>
                                <div><strong>Report Format:</strong> ${reportFormat}</div>
                                <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });

            // Delete radiology test
            $(document).on('click', '.delete-test', function() {
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
                            url: '/api/radiology-tests/' + testId,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Deleted!',
                                        'Radiology test deleted successfully!',
                                        'success')
                                    .then(() => {
                                        if ($.fn.DataTable.isDataTable("#radiologytesttbl")) {
                                            $('#radiologytesttbl').DataTable().ajax.reload(null, false);
                                        } else {
                                            location.reload();
                                        }
                                    });
                            },
                            error: function(xhr) {
                                let errorMsg = 'Failed to delete test.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            // View radiology test
            $(document).on('click', '.view-test', function() {
                var testId = $(this).data('id');
                window.location.href = '/radiology-tests/' + testId;
            });

            // Edit radiology test
            $(document).on('click', '.edit-test', function() {
                var testId = $(this).data('id');
                window.location.href = '/radiology-tests/edit/' + testId;
            });

            // Export button
            $(document).on('click', '#exportButton', function() {
                let branchId = localStorage.getItem('selectedBranchId');
                if (!branchId) {
                    alert("Please select a branch first.");
                    return;
                }
                window.location.href = "{{ route('radio-test.export') }}" + "?branch_id=" + branchId;
            });
        });
    </script>
@endsection
