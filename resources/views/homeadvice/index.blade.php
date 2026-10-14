@extends('layout.app')

<style>
    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    a.btn.btn-sm.downloadpdf {
        background: #f89884;
        border-radius: 10px;
        padding-top: 7px;
        color: white;
        width: 32px;
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

    #treatmenttbl thead th {
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
                                <i class="fa fa-calendar-check-o px-2" style="font-size:20px"></i>
                                All Homeadvice
                            </h3>

                            {{-- âœ… Export Button --}}
                            <button class="btn btn-rounded btn-hdr" id="homeAdviceExportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>


                            @if (app('hasPermission')(28, 'create'))
                                <a href="{{ route('homeadvice.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="treatmenttbl" class="table">
                                    <thead>
                                        <tr>
                                            <th>Template Name</th>
                                            <th>Titles</th>
                                            {{-- <th>Descriptions</th>
                                            <th>Actions</th> --}}
                                             <th class="d-none d-md-table-cell">Descriptions</th>
                                             <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="homeadviceTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTables & SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).on('click', '#homeAdviceExportButton', function() {
        let branchId = localStorage.getItem('selectedBranchId');

        if (!branchId) {
            alert("Please select a branch first.");
            return;
        }

        window.location.href = "{{ route('homeadvice.export') }}" + "?branch_id=" + branchId;
    });

    $(document).ready(function() {
        let branchId = localStorage.getItem('selectedBranchId');
        let token = localStorage.getItem("token");
        let homeAdviceTable;

        function ucfirst(str) {
            if (!str) return 'N/A';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function renderList(items) {
            if (!items || items.length === 0) return '<em>N/A</em>';
            let html = '<ul style="padding-left: 20px;">';
            items.forEach(item => {
                html += `<li>${item ? ucfirst(item) : 'N/A'}</li>`;
            });
            html += '</ul>';
            return html;
        }

        function safeArray(value) {
            if (Array.isArray(value)) return value;
            try {
                return JSON.parse(value || '[]');
            } catch (e) {
                return [];
            }
        }

        function buildActions(homeadvice) {
            let actionsHtml = `<div class="icon" style="cursor:pointer;">`;
            @if (app('hasPermission')(29, 'view'))
                actionsHtml += `<i class="fa fa-eye m-r-5 icon3 view-homeadvice" data-id="${homeadvice.id}" title="View"></i>`;
            @endif
            @if (app('hasPermission')(29, 'update'))
                actionsHtml += `<i class="fa fa-pencil m-r-5 icon1 edit-homeadvice" data-id="${homeadvice.id}" title="Edit"></i>`;
            @endif
            @if (app('hasPermission')(29, 'delete'))
                actionsHtml += `<i class="fa fa-trash-o m-r-5 icon2 delete-homeadvice" data-id="${homeadvice.id}" title="Delete"></i>`;
            @endif

            let downloadUrl = `{{ route('homeadvice.download', ':id') }}`.replace(':id', homeadvice.id);
            actionsHtml += `<a href="${downloadUrl}" class="btn btn-sm downloadpdf" title="Download PDF">
                <i class="fa-solid fa-download"></i>
            </a>`;
            actionsHtml += `</div>`;
            return actionsHtml;
        }

        // expose helpers for mobile expand handler
        window.homeAdviceRenderList = renderList;
        window.homeAdviceSafeArray = safeArray;
        window.homeAdviceBuildActions = buildActions;

        function initHomeAdviceTable() {
            if ($.fn.DataTable.isDataTable('#treatmenttbl')) {
                try {
                    let existingTable = $('#treatmenttbl').DataTable();
                    existingTable.destroy();
                    $('#treatmenttbl').removeClass('dataTable');
                    $('#treatmenttbl tbody').empty();
                    $.removeData($('#treatmenttbl')[0], 'DataTable');
                    $.removeData($('#treatmenttbl')[0], 'DataTables_DataTable');
                    $('#treatmenttbl').off();
                } catch (e) {
                    $('#treatmenttbl tbody').empty();
                    $.removeData($('#treatmenttbl')[0]);
                }
            }

            if ($('#treatmenttbl tbody').length === 0) {
                $('#treatmenttbl').append('<tbody id="homeadviceTableBody"></tbody>');
            }

            setTimeout(function() {
                homeAdviceTable = $('#treatmenttbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/homeadvice',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                branch_id: branchId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            headers: { "Authorization": "Bearer " + token },
                            success: function(response) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: response.pagination?.total || 0,
                                    recordsFiltered: response.pagination?.total || 0,
                                    data: response.data || []
                                });
                            },
                            error: function(xhr) {
                                console.log("Error loading homeadvices:", xhr.responseText);
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
                            data: 'template_name',
                            render: function(data) {
                                return ucfirst(data ?? 'N/A');
                            }
                        },
                        {
                            data: 'title',
                            render: function(data, type, row) {
                                const titles = safeArray(row.title);
                                return renderList(titles);
                            }
                        },
                        {
                            data: 'description',
                            render: function(data, type, row) {
                                const descriptions = safeArray(row.description);
                                return renderList(descriptions);
                            },
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: 'id',
                            render: function(data, type, row) {
                                return buildActions(row);
                            },
                            className: 'd-none d-md-table-cell'
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
                            className: 'd-table-cell d-md-none text-center',
                            orderable: false
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ],
                    pageLength: 10,
                    responsive: false
                });
                window.homeAdviceTable = homeAdviceTable;
            }, 50);
        }

        function reloadHomeAdviceTable() {
            if (!homeAdviceTable) {
                initHomeAdviceTable();
                return;
            }
            homeAdviceTable.ajax.reload(null, false);
        }

        initHomeAdviceTable();

        // View
        $(document).on('click', '.view-homeadvice', function() {
            var homeadviceId = $(this).data('id');
            window.location.href = '/homeadvice/show/' + homeadviceId;
        });

        // Edit
        $(document).on('click', '.edit-homeadvice', function() {
            var homeadviceId = $(this).data('id');
            window.location.href = '/homeadvice/edit/' + homeadviceId;
        });

        // Delete
        $(document).on('click', '.delete-homeadvice', function() {
            var homeadviceId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this homeadvice!",
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
                        url: '/api/homeadvice/' + homeadviceId,
                        type: 'DELETE',
                        headers: { "Authorization": "Bearer " + token },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Therapy deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                reloadHomeAdviceTable();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to delete homeadvice.', 'error');
                        }
                    });
                }
            });
        });
    });

    // === Mobile dropdown expand (Details button) ===
    $(document).on("click", ".expand-btn", function (e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");

        // Close any other open details
        $(".details-row").not(tr.next(".details-row")).remove();
        $(".expand-btn i").not(icon).removeClass("fa-chevron-up").addClass("fa-chevron-down");

        const existingRow = tr.next(".details-row");
        if (existingRow.length) {
            existingRow.slideUp(200, function () { $(this).remove(); });
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        if (!window.homeAdviceTable || !$.fn.DataTable.isDataTable('#treatmenttbl')) {
            return;
        }

        const row = window.homeAdviceTable.row(tr);
        const data = row.data() || {};
        const descriptions = window.homeAdviceRenderList(window.homeAdviceSafeArray(data.description));
        const actionHtml = window.homeAdviceBuildActions(data);

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="5">
                    <div class="details-content">
                        <div><strong>Description:</strong> ${descriptions}</div>
                        <div class="mt-2"><strong>Actions:</strong> ${actionHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });
</script>
@endsection

