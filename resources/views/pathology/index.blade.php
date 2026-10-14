@extends('layout.app')

<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    .details-row {
        background-color: #f8f9fa;
        display: none;
    }

    colgroup {
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


    @media screen and (max-width: 767px) {
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
                                <i class="fa fa-flask px-2" style="font-size:20px"></i> Pathology Tests
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(19, 'create'))
                                <a href="{{ route('pathology.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> Add Test
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="pathologytbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Test Code</th>
                                            <th class="d-none d-md-table-cell">Sample Type</th>
                                            <th class="d-none d-md-table-cell">Normal Range</th>
                                            <th class="d-none d-md-table-cell">Cost</th>
                                            <th class="d-none d-md-table-cell">Report Format</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- Mobile-only column -->
                                        </tr>
                                    </thead>
                                    <tbody id="pathologyTableBody">
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

    {{-- JS & DataTable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    <script>
        $('#exportButton').on('click', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('pathology.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function() {
            function ucfirst(str) {
                return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
            }
            let branchId = localStorage.getItem('selectedBranchId');
            let token = localStorage.getItem("authToken");

            function renderCost(cost) {
                if (cost === null || cost === undefined || cost === '') return 'N/A';
                const num = parseFloat(cost);
                return isNaN(num) ? 'N/A' : `₹ ${num.toFixed(2)}`;
            }

            function renderActions(test) {
                return `
                    <div class="icon">
                        @if (app('hasPermission')(19, 'view'))<i class="fa fa-eye m-r-5 icon3 view-test" style="cursor:pointer" data-id="${test.id}"></i>@endif
                        @if (app('hasPermission')(19, 'update'))<i class="fa fa-pencil m-r-5 icon1 edit-test" style="cursor:pointer" data-id="${test.id}"></i>@endif
                        @if (app('hasPermission')(19, 'delete'))<i class="fa fa-trash-o m-r-5 icon2 delete-test" style="cursor:pointer" data-id="${test.id}"></i>@endif
                    </div>
                `;
            }

            function initPathologyTable() {
                if ($.fn.DataTable.isDataTable("#pathologytbl")) {
                    try {
                        let existingTable = $('#pathologytbl').DataTable();
                        existingTable.destroy();
                        $('#pathologytbl').removeClass('dataTable');
                        $('#pathologytbl tbody').empty();
                        $.removeData($('#pathologytbl')[0], 'DataTable');
                        $.removeData($('#pathologytbl')[0], 'DataTables_DataTable');
                        $('#pathologytbl').off();
                    } catch (e) {
                        $('#pathologytbl tbody').empty();
                        $.removeData($('#pathologytbl')[0]);
                    }
                }

                if ($('#pathologytbl tbody').length === 0) {
                    $('#pathologytbl').append('<tbody id="pathologyTableBody"></tbody>');
                }

                setTimeout(function () {
                    $('#pathologytbl').DataTable({
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
                                url: '/api/pathology-tests',
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
                                    const rows = response.pathology || response.data || response || [];
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
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${ucfirst(data)}</span>`;
                                }
                            },
                            {
                                data: "test_code",
                                render: function (data, type, row) {
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${ucfirst(data)}</span>`;
                                }
                            },
                            {
                                data: "sample_type",
                                render: function (data, type, row) {
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${ucfirst(data)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "normal_range",
                                render: function (data, type, row) {
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${ucfirst(data)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "cost",
                                render: function (data, type, row) {
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${renderCost(data)}</span>`;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "report_format",
                                render: function (data, type, row) {
                                    return `<span class="view-test" style="cursor:pointer" data-id="${row.id}">${ucfirst(data)}</span>`;
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

            initPathologyTable();

            // Handle expandable rows for mobile
            $(document).on('click', '.expand-btn', function() {
                const btn = $(this);
                const icon = btn.find('i');
                const tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#pathologytbl')) {
                    return;
                }

                const table = $('#pathologytbl').DataTable();
                const row = table.row(tr);
                const data = row.data();
                if (!data) return;

                const existingRow = tr.next('.details-row');
                if (existingRow.length) {
                    existingRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                const sampleType = ucfirst(data.sample_type);
                const normalRange = ucfirst(data.normal_range);
                const cost = renderCost(data.cost);
                const reportFormat = ucfirst(data.report_format);
                const actionHtml = renderActions(data);

                const detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="3">
                            <div class="details-content">
                                <div><strong>Sample Type:</strong> ${sampleType}</div>
                                <div><strong>Normal Range:</strong> ${normalRange}</div>
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

            // Delete test
            $(document).on('click', '.delete-test', function() {
                let id = $(this).data('id');
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
                            url: `/api/pathology-tests/${id}`,
                            type: 'DELETE',
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success');
                                if ($.fn.DataTable.isDataTable("#pathologytbl")) {
                                    $('#pathologytbl').DataTable().ajax.reload(null, false);
                                } else {
                                    location.reload();
                                }
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

            // View test
            $(document).on('click', '.view-test', function() {
                let id = $(this).data('id');
                window.location.href = `/pathology/show/${id}`;
            });

            // Edit test
            $(document).on('click', '.edit-test', function() {
                let id = $(this).data('id');
                window.location.href = `/pathology/edit/${id}`;
            });
        });
    </script>
@endsection
