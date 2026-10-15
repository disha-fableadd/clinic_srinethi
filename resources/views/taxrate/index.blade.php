@extends('layout.app')
<style>
    .custom-close {
        background-color: #f5b6a5 !important;
        opacity: 1;
        border: 1px solid #f5b6a5;
        border-radius: 5px;
        padding: 3px 6px;
    }

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    .swal-cancel-btn {
        color: white !important;
    }

    colgroup {
        display: none;
    }

    button.btn.btn-link.expand-btn {
        background: #f89884 !important;
        color: white !important;
        padding: 7px 10px !important;
        border-radius: 9px !important;
    }

    button.btn.expand-btn {
        background: #f89884 !important;
        color: white !important;
        padding: 7px 10px !important;
        border-radius: 9px !important;
    }

    /* Clean action icon styling consistent with other tables */
    .icon {
        font-size: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .icon .icon1,
    .icon .icon2 {
        padding: 8px;
        background-color: #f89884;
        color: white;
        border-radius: 10px;
        margin: 0 4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: none;
    }

    .icon .icon1:hover,
    .icon .icon2:hover {
        background-color: #e58773;
        color: white;
    }

    .details-row {
        background-color: #f8f9fa;
        display: none;
    }

    .details-content {
        padding: 15px;
        border-left: 4px solid #f89884;
        margin-left: 10px;
        line-height: 1.8;
    }

    .details-content strong {
        color: #333;
    }

    .expand-btn i {
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    #taxratetbl thead th {
        vertical-align: middle !important;
        padding: 10px 8px !important;
        color: white !important;
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
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    /* GST Toggle Switch styling in Card Header */
    .gst-header-toggle-wrap {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.5);
        padding: 4px 12px;
        border-radius: 25px;
        margin-right: 12px;
        vertical-align: middle;
        backdrop-filter: blur(4px);
    }

    .gst-header-toggle-label {
        font-weight: 600;
        font-size: 13.5px;
        color: #ffffff;
        margin-right: 10px;
        margin-bottom: 0;
        user-select: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .gst-switch-ui {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
        margin-bottom: 0;
    }

    .gst-switch-ui input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .gst-slider-ui {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 22px;
    }

    .gst-slider-ui:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .gst-switch-ui input:checked + .gst-slider-ui {
        background-color: #28a745;
    }

    .gst-switch-ui input:checked + .gst-slider-ui:before {
        transform: translateX(22px);
    }

    .gst-status-badge {
        font-size: 11.5px;
        font-weight: 700;
        color: #ffffff;
        margin-left: 8px;
        letter-spacing: 0.5px;
        min-width: 28px;
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
                                <i class="fa fa-percent px-2" style="font-size:20px"></i> All Tax Rates
                            </h3>
                            <div class="d-inline-flex align-items-center">
                                @php
                                    $currentGstStatus = \App\Models\Setting::getValue('gst_status', 'on');
                                @endphp
                                <div class="gst-header-toggle-wrap" title="Enable or Disable GST across the entire application">
                                    <label class="gst-header-toggle-label" for="globalGstStatusToggle">
                                        <i class="fas fa-file-invoice-dollar"></i> GST
                                    </label>
                                    <label class="gst-switch-ui">
                                        <input type="checkbox" id="globalGstStatusToggle" {{ $currentGstStatus === 'on' ? 'checked' : '' }}>
                                        <span class="gst-slider-ui"></span>
                                    </label>
                                    <span class="gst-status-badge" id="globalGstStatusText">{{ strtoupper($currentGstStatus) }}</span>
                                </div>
                                @if (app('hasPermission')(36, 'create'))
                                    <a href="" class="btn btn-rounded btn-hdr" data-bs-toggle="modal"
                                        data-bs-target="#addTaxRateModal">
                                        <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="taxratetbl" class="table custom-table">
                                    <thead style="background-color:#f89884;" class="text-center">
                                        <tr>
                                            <th>Taxname</th>
                                            <th class="d-none d-md-table-cell">Tax%</th>
                                            <th class="d-none d-md-table-cell">Status</th>
                                            <th class="d-none d-md-table-cell text-center">Action</th>
                                            <th class="d-table-cell d-md-none text-center">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Tax Rate Modal -->
        <div class="modal fade" id="addTaxRateModal" tabindex="-1" aria-labelledby="addTaxRateModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="taxRateForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="addTaxRateModalLabel">Add Tax Rate</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="tax_name" class="form-label">Tax Name</label>
                                <input type="text" class="form-control" id="tax_name" name="tax_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="tax_rate" class="form-label">Tax %</label>
                                <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div id="taxRateSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="taxRateError" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Tax Rate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Tax Rate Modal -->
        <div class="modal fade" id="editTaxRateModal" tabindex="-1" aria-labelledby="editTaxRateModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="editTaxRateForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTaxRateId" name="id">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="editTaxRateModalLabel">Edit Tax Rate</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_tax_name" class="form-label">Tax Name</label>
                                <input type="text" class="form-control" id="edit_tax_name" name="tax_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_tax_rate" class="form-label">Tax %</label>
                                <input type="number" step="0.01" class="form-control" id="edit_tax_rate"
                                    name="tax_rate" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div id="editTaxRateSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="editTaxRateError" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Tax Rate</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- External CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {


            if (window.__taxRatePageInitialized) {
                return;
            }
            window.__taxRatePageInitialized = true;
            let branchId = localStorage.getItem('selectedBranchId');
            const token = localStorage.getItem('token');
            let taxRateTable;

            // Initialize select2
            if ($('#status').length) {
                $('#status').select2({
                    width: '100%',
                    dropdownParent: $('#addTaxRateModal'),
                    placeholder: "Select Status"
                });
            }

            if ($('#edit_status').length) {
                $('#edit_status').select2({
                    width: '100%',
                    dropdownParent: $('#editTaxRateModal'),
                    placeholder: "Select Status"
                });
            }

            function initializeDataTable() {
                if ($.fn.DataTable.isDataTable('#taxratetbl')) {
                    try {
                        let existingTable = $('#taxratetbl').DataTable();
                        existingTable.destroy();
                        $('#taxratetbl').removeClass('dataTable');
                        $('#taxratetbl tbody').empty();
                        $.removeData($('#taxratetbl')[0], 'DataTable');
                        $.removeData($('#taxratetbl')[0], 'DataTables_DataTable');
                        $('#taxratetbl').off();
                    } catch (e) {
                        $('#taxratetbl tbody').empty();
                        $.removeData($('#taxratetbl')[0]);
                    }
                }

                if ($('#taxratetbl tbody').length === 0) {
                    $('#taxratetbl').append('<tbody></tbody>');
                }

                setTimeout(function() {
                    taxRateTable = $('#taxratetbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        paging: true,
                        searching: true,
                        ordering: true,
                        responsive: true,
                        autoWidth: false,
                        ajax: function(data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            const perPage = data.length;
                            const req = {
                                branch_id: branchId,
                                user_id: (typeof userId !== 'undefined' && userId) ?
                                    userId : null,
                                page: page,
                                per_page: perPage,
                                search: data.search?.value || ''
                            };
                            $.ajax({
                                url: '/api/tax-rates',
                                type: 'GET',
                                data: req,
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function(response) {
                                    callback({
                                        draw: parseInt(data.draw),
                                        data: response.data || [],
                                        recordsTotal: response.pagination
                                            ?.total || 0,
                                        recordsFiltered: response.pagination
                                            ?.total || 0
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('DataTable AJAX error:', error);
                                    callback({
                                        draw: parseInt(data.draw),
                                        data: [],
                                        recordsTotal: 0,
                                        recordsFiltered: 0
                                    });
                                }
                            });
                        },
                        columnDefs: [{
                            orderable: false,
                            targets: [3, 4]
                        }],
                        columns: [{
                                data: "tax_name",
                                defaultContent: "N/A",
                                className: "text-center"
                            },
                            {
                                data: "tax_rate",
                                defaultContent: "N/A",
                                className: "d-none d-md-table-cell text-center"
                            },
                            {
                                data: "status",
                                defaultContent: "N/A",
                                render: function(data) {
                                    if (!data) return "N/A";
                                    let statusText = data.charAt(0).toUpperCase() + data
                                        .slice(1);
                                    let statusClass = (data.toLowerCase() === 'active') ?
                                        'status-active' : 'status-inactive';
                                    return `<span class="${statusClass}">${statusText}</span>`;
                                },
                                className: "d-none d-md-table-cell text-center"
                            },
                            {
                                data: null,
                                render: function(data, type, row) {
                                    let buttons = '<div class="icon" style="cursor:pointer;">';
                                    @if (app('hasPermission')(36, 'update'))
                                        buttons += `<i class="fa fa-pencil m-r-5 icon1 edit-taxrate" data-id="${row.id}" title="Edit"></i>`;
                                    @endif
                                    @if (app('hasPermission')(36, 'delete'))
                                        buttons += `<i class="fa fa-trash-o m-r-5 icon2 delete-taxrate" data-id="${row.id}" title="Delete"></i>`;
                                    @endif
                                    buttons += '</div>';
                                    return buttons;
                                },
                                className: "d-none d-md-table-cell text-center",
                                orderable: false
                            },
                            {
                                data: null,
                                render: function() {
                                    return `<button class="btn expand-btn"><i class="fa fa-chevron-down"></i></button>`;
                                },
                                orderable: false,
                                className: "text-center d-table-cell d-md-none"
                            }
                        ],
                        initComplete: function() {
                            console.log('DataTable initialized successfully');
                        },
                        language: {
                            emptyTable: "No tax rates found",
                            // processing: "Loading..."
                        }
                    });

                    window.__taxRateDataTable = taxRateTable;
                }, 50);
            }

            // Separate function for expand button click to avoid duplication
            function handleExpandClick(e) {
                e.stopPropagation();
                const btn = $(this);
                const icon = btn.find("i");
                const tr = btn.closest("tr");

                if (!taxRateTable || !$.fn.DataTable.isDataTable('#taxratetbl')) {
                    return;
                }

                const row = taxRateTable.row(tr);
                const data = row.data();

                if (!data) {
                    console.error('No data found for row');
                    return;
                }

                const existingRow = tr.next(".details-row");
                if (existingRow.length) {
                    existingRow.slideToggle(300);
                    icon.toggleClass("fa-chevron-down fa-chevron-up");
                    return;
                }

                const taxRateValue = data.tax_rate || "N/A";
                const statusText = data.status ? (data.status.charAt(0).toUpperCase() + data.status.slice(1)) :
                    "N/A";
                const statusClass = (data.status && data.status.toLowerCase() === 'active') ? 'status-active' :
                    'status-inactive';
                const statusHtml = `<span class="${statusClass}">${statusText}</span>`;

                const actionButtons = `
                <div class="mt-2">
                    <strong>Actions:</strong>
                    <div class="icon mt-2" style="cursor:pointer; justify-content: flex-start;">
                        @if (app('hasPermission')(36, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-taxrate" data-id="${data.id}" title="Edit"></i>
                        @endif
                        @if (app('hasPermission')(36, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-taxrate" data-id="${data.id}" title="Delete"></i>
                        @endif
                    </div>
                </div>
            `;

                const detailsRow = $(`
                <tr class="details-row">
                    <td colspan="5">
                        <div class="details-content">
                            <div><strong>Tax %:</strong> ${taxRateValue}</div>
                            <div><strong>Status:</strong> ${statusHtml}</div>
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `);

                tr.after(detailsRow);
                detailsRow.slideDown(300);
                icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
            }

            // Initialize the DataTable
            initializeDataTable();

            // Expand handlers (delegate like discharge table)
            $(document).off('click', '.expand-btn').on('click', '.expand-btn', handleExpandClick);

            $(window).on('resize', function() {
                if ($(window).width() >= 768) {
                    $('.details-row').remove();
                    $('.expand-btn i')
                        .removeClass('fa-chevron-up')
                        .addClass('fa-chevron-down');
                }
            });

            // Add Tax Rate Form Submit
            $('#taxRateForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                $('#taxRateSuccess,#taxRateError').hide();

                $.ajax({
                    url: "/api/tax-rates",
                    method: "POST",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: {
                        tax_name: $('#tax_name').val(),
                        tax_rate: $('#tax_rate').val(),
                        status: $('#status').val(),
                        branch_id: branchId
                    },
                    success: function(response) {
                        $('#taxRateSuccess').text(response.message).fadeIn();
                        setTimeout(() => {
                            $('#addTaxRateModal').modal('hide');
                            $('#taxRateForm')[0].reset();
                            $('#taxRateSuccess').fadeOut();
                            if (taxRateTable) {
                                taxRateTable.ajax.reload(null, false);
                            }
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || 'Something went wrong!';
                        if (xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                ', ');
                        }
                        $('#taxRateError').text(errorMessage).fadeIn();
                        setTimeout(() => $('#taxRateError').fadeOut(), 3000);
                    }
                });
            });

            // Edit Tax Rate
            $(document).off('click', '.edit-taxrate').on('click', '.edit-taxrate', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: '/api/tax-rates/' + id,
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(tax) {
                        $('#editTaxRateId').val(tax.id);
                        $('#edit_tax_name').val(tax.tax_name);
                        $('#edit_tax_rate').val(tax.tax_rate);
                        $('#edit_status').val(tax.status).trigger('change');
                        $('#editTaxRateModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to fetch tax rate data.', 'error');
                    }
                });
            });

            // Update Tax Rate Form Submit
            $('#editTaxRateForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editTaxRateId').val();
                $('#editTaxRateSuccess,#editTaxRateError').hide();

                $.ajax({
                    url: '/api/tax-rates/' + id,
                    type: 'PUT',
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    data: {
                        tax_name: $('#edit_tax_name').val(),
                        tax_rate: $('#edit_tax_rate').val(),
                        status: $('#edit_status').val(),
                        branch_id: branchId
                    },
                    success: function(response) {
                        $('#editTaxRateSuccess').text(response.message).fadeIn();
                        setTimeout(() => {
                            $('#editTaxRateModal').modal('hide');
                            $('#editTaxRateSuccess').fadeOut();
                            if (taxRateTable) {
                                taxRateTable.ajax.reload(null, false);
                            }
                        }, 1500);
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || 'Something went wrong!';
                        if (xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                ', ');
                        }
                        $('#editTaxRateError').text(errorMessage).fadeIn();
                        setTimeout(() => $('#editTaxRateError').fadeOut(), 3000);
                    }
                });
            });

            // Delete Tax Rate
            $(document).off('click', '.delete-taxrate').on('click', '.delete-taxrate', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/tax-rates/' + id,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                if (taxRateTable) {
                                    taxRateTable.ajax.reload(null, false);
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Failed to delete tax rate.', 'error');
                            }
                        });
                    }
                });
            });

            // Handle modal close events to clean up select2
            $('#addTaxRateModal').on('hidden.bs.modal', function() {
                $('#taxRateForm')[0].reset();
                $('#taxRateSuccess,#taxRateError').hide();
            });

            $('#editTaxRateModal').on('hidden.bs.modal', function() {
                $('#editTaxRateSuccess,#editTaxRateError').hide();
            });

            // Toggle Global GST Status
            $('#globalGstStatusToggle').on('change', function () {
                let isChecked = $(this).is(':checked');
                let newStatus = isChecked ? 'on' : 'off';
                let $toggle = $(this);
                let $statusText = $('#globalGstStatusText');

                $statusText.text(newStatus.toUpperCase());

                $.ajax({
                    url: '/api/save-service-status',
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: {
                        type: 'gst',
                        status: newStatus
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: newStatus === 'on' ? 'success' : 'info',
                            title: 'GST ' + (newStatus === 'on' ? 'Enabled' : 'Disabled'),
                            text: 'GST has been ' + (newStatus === 'on' ? 'enabled' : 'disabled') + ' across all modules.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        // Revert switch on failure
                        $toggle.prop('checked', !isChecked);
                        $statusText.text((!isChecked) ? 'ON' : 'OFF');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to update GST status.'
                        });
                    }
                });
            });
        });
    </script>
@endsection
