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

    colgroup {
        display: none;
    }

    .card-header {
        background-color: #f89884 !important;
    }

    .btn-rounded {
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

    .status-active {
        background-color: #cfece0 !important;
        color: black;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    .status-inactive {
        background-color: #f89884 !important;
        color: white !important;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        min-width: 80px;
        text-align: center;
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
                                <i class="fa fa-flask px-2" style="font-size:20px"></i> Therapies
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(26, 'create'))
                                <a href="{{ route('therapy.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> Add Therapy
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="therapytbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="d-none d-md-table-cell">Duration (minutes)</th>
                                            <th class="d-none d-md-table-cell">Cost (₹)</th>
                                            <th class="d-none d-md-table-cell">Status</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- Mobile-only column -->
                                        </tr>
                                    </thead>

                                    <tbody id="therapyTableBody">
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
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('therapy.export') }}" + "?branch_id=" + branchId;
        });

        $(document).ready(function() {
            fetchTherapies();
        });

        let therapyTable;

        function ucfirst(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
        }

        function fetchTherapies() {
            let branchId = localStorage.getItem('selectedBranchId');

            if ($.fn.DataTable.isDataTable("#therapytbl")) {
                try {
                    let existingTable = $('#therapytbl').DataTable();
                    existingTable.destroy();
                    $('#therapytbl').removeClass('dataTable');
                    $('#therapytbl tbody').empty();
                    $.removeData($('#therapytbl')[0], 'DataTable');
                    $.removeData($('#therapytbl')[0], 'DataTables_DataTable');
                    $('#therapytbl').off();
                } catch (e) {
                    $('#therapytbl tbody').empty();
                    $.removeData($('#therapytbl')[0]);
                }
            }

            if ($('#therapytbl tbody').length === 0) {
                $('#therapytbl').append('<tbody></tbody>');
            }

            setTimeout(function() {
                therapyTable = $('#therapytbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/therapies',
                            type: 'GET',
                            data: {
                                branch_id: branchId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            dataType: 'json',
                            success: function(json) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: json.therapies || []
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
                                Swal.fire('Error', 'Failed to load therapy data', 'error');
                            }
                        });
                    },
                    columns: [{
                            data: 'name',
                            render: function(data, type, row) {
                                return `${ucfirst(data ?? 'N/A')}`;
                            },
                            className: "view-therapy",
                            createdCell: function(td, cellData, rowData) {
                                $(td).attr('data-id', rowData.id);
                            }
                        },
                        {
                            data: 'description',
                            render: data => data ?? 'N/A',
                            className: "view-therapy"
                        },
                        {
                            data: 'duration_minutes',
                            render: data => data ?? 'N/A',
                            className: "d-none d-md-table-cell view-therapy text-center",
                            width: "10%"
                        },
                        {
                            data: 'cost',
                            render: function(data) {
                                let cost = data ?? 0;
                                return `₹ ${parseFloat(cost).toFixed(2)}`;
                            },
                            className: "d-none d-md-table-cell view-therapy",
                            width: "15%"
                        },
                        {
                            data: 'status',
                            render: function(data) {
                                let statusText = ucfirst(data ?? 'N/A');
                                let statusClass = (data && data.toLowerCase() === 'active') ?
                                    'status-active' : 'status-inactive';
                                return `<span class="${statusClass}">${statusText}</span>`;
                            },
                            className: "d-none d-md-table-cell view-therapy text-center"
                        },
                        {
                            data: 'id',
                            render: function(data) {
                                return `
                                <div class="icon">
                                    @if (app('hasPermission')(26, 'view'))<i class="fa fa-eye m-r-5 icon3 view-therapy" style="cursor:pointer" data-id="${data}" title="View"></i>@endif
                                    @if (app('hasPermission')(26, 'update'))<i class="fa fa-pencil m-r-5 icon1 edit-therapy" style="cursor:pointer" data-id="${data}" title="Edit"></i>@endif
                                    @if (app('hasPermission')(26, 'delete'))<i class="fa fa-trash-o m-r-5 icon2 delete-therapy" style="cursor:pointer" data-id="${data}" title="Delete"></i>@endif
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
                        [0, 'asc']
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

            let row = therapyTable.row(tr);
            let data = row.data();

            const duration = data?.duration_minutes ?? 'N/A';
            const cost = data?.cost ?? 0;
            const statusText = ucfirst(data?.status ?? 'N/A');
            const statusClass = (data?.status && data.status.toLowerCase() === 'active') ? 'status-active' :
                'status-inactive';
            const statusHtml = `<span class="${statusClass}">${statusText}</span>`;
            const actionHtml = `
                <div class="icon">
                    @if (app('hasPermission')(26, 'view'))<i class="fa fa-eye m-r-5 icon3 view-therapy" style="cursor:pointer" data-id="${data?.id}" title="View"></i>@endif
                    @if (app('hasPermission')(26, 'update'))<i class="fa fa-pencil m-r-5 icon1 edit-therapy" style="cursor:pointer" data-id="${data?.id}" title="Edit"></i>@endif
                    @if (app('hasPermission')(26, 'delete'))<i class="fa fa-trash-o m-r-5 icon2 delete-therapy" style="cursor:pointer" data-id="${data?.id}" title="Delete"></i>@endif
                </div>
            `;

            const detailsRow = $(`
                <tr class="details-row">
                    <td colspan="3">
                        <div class="details-content">
                            <div><strong>Duration:</strong> ${duration}</div>
                            <div><strong>Cost:</strong> ₹ ${parseFloat(cost).toFixed(2)}</div>
                            <div><strong>Status:</strong> ${statusHtml}</div>
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

        // Delete therapy
        $(document).on('click', '.delete-therapy', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this therapy?",
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
                        url: `/api/therapies/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            location.reload();
                        },
                        error: function(xhr) {
                            let errorMsg = 'Failed to delete therapy.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        });

        // View therapy
        $(document).on('click', '.view-therapy', function() {
            let id = $(this).data('id');
            window.location.href = `/therapy/show/${id}`;
        });

        // Edit therapy
        $(document).on('click', '.edit-therapy', function() {
            let id = $(this).data('id');
            window.location.href = `/therapy/edit/${id}`;
        });
    </script>
@endsection
