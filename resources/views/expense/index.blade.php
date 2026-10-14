@extends('layout.app')

<style>
    .row.mb-3.filter {
        margin-top: 21px;
        margin-left: 10px;
    }

    .swal-confirm-btn {
        color: rgb(58, 58, 58) !important;
    }

    /* Cancel button text white (for visibility on red background) */
    .swal-cancel-btn {
        color: white !important;
    }

    @media screen and (max-width: 767px) {
        .page-title {
            font-size: 20px !important;
            padding-left: 7px !important;
        }

        .filter .col-12 {
            padding-right: 10px !important;
            padding-left: 10px !important;
        }
    }

    .filter {
        border-color: rgb(248 152 132) !important;
    }

    /* âœ… Mobile detail view styling */
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
            <div class="row pt-0 pt-md-3">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title m-0" style="text-align:left;">All Expenses</h4>
                </div>
            </div>

            {{-- FILTER BAR --}}


            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="">
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-money px-2" style="font-size:20px"></i> Expenses
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            @if (app('hasPermission')(33, 'create'))
                                <a href="{{ route('expense.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="row mb-3 filter ">

                                <div class="col-6 col-md-3 mb-2 px-2">
                                <select id="filterMonth" class="form-control select2 filter">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                                <div class="col-6 col-md-3 mb-2 px-2 ">
                                <select id="filterYear" class="form-control select2 filter">
                                    <option value="">All Years</option>
                                    @for ($y = date('Y'); $y >= 2000; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>




                        </div>

                            <div class="table-responsive">
                                <table id="expensestbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Staff</th>
                                            <th>Date & time</th>
                                            <th class="d-none d-md-table-cell">Amount (₹)</th>
                                            <th class="d-none d-md-table-cell">Service</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- âœ… for mobile expand -->
                                        </tr>
                                    </thead>
                                    <tbody id="expenseTableBody">

                                    </tbody>


                                </table>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-md-12 text-center">
                                <h5 style="margin:0;font-size:15px;color:black;font-weight:500">Grand Total: <span
                                        id="grandTotal">₹ 0.00</span></h5>

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


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    {{-- <script>
    $('#filterMonth').select2({
        placeholder: "Select Month",
        width: '100%'
    });
    $('#filterMonth').on('select2:open', function() {
        $('.select2-search__field').attr('placeholder', 'Search Month');
    });
    $('#filterYear').select2({
        placeholder: "Select Year",
        width: '100%'
    });
    $('#filterYear').on('select2:open', function() {
        $('.select2-search__field').attr('placeholder', 'Search Year');
    });

    $(document).on('click', '#exportButton', function() {
        let branchId = localStorage.getItem("selectedBranchId");

        if (!branchId) {
            alert("Please select a branch first.");
            return;
        }

        window.location.href = "{{ route('expense.export') }}" + "?branch_id=" + branchId;
    });

    // Load filter options
    $.ajax({
        url: '/api/expenses/filters',
        type: 'GET',
        success: function(res) {
            // Year
            $('#filterYear').empty().append('<option value="">All Years</option>');
            res.years.forEach(year => {
                $('#filterYear').append(`<option value="${year}">${year}</option>`);
            });

            // Staff
            $('#filterStaff').empty().append('<option value="">All Staff</option>');
            res.staff.forEach(s => {
                $('#filterStaff').append(`<option value="${s.id}">${s.name}</option>`);
            });
        }
    });

    $(document).ready(function() {
        let branchId = localStorage.getItem('selectedBranchId');
        let expenseTable;

        function ucfirst(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
        }

        function initExpenseTable() {
            if ($.fn.DataTable.isDataTable('#expensestbl')) {
                try {
                    let existingTable = $('#expensestbl').DataTable();
                    existingTable.destroy();
                    $('#expensestbl').removeClass('dataTable');
                    $('#expensestbl tbody').empty();
                    $.removeData($('#expensestbl')[0], 'DataTable');
                    $.removeData($('#expensestbl')[0], 'DataTables_DataTable');
                    $('#expensestbl').off();
                } catch (e) {
                    $('#expensestbl tbody').empty();
                    $.removeData($('#expensestbl')[0]);
                }
            }

            if ($('#expensestbl tbody').length === 0) {
                $('#expensestbl').append('<tbody id="expenseTableBody"></tbody>');
            }

            setTimeout(function() {
                expenseTable = $('#expensestbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/expenses',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                branch_id: branchId,
                                month: $('#filterMonth').val(),
                                year: $('#filterYear').val(),
                                user_id: $('#filterStaff').val(),
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            success: function(json) {
                                const rows = json.data || [];
                                const grandTotal = rows.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
                                $('#grandTotal').text(`₹ ${grandTotal.toFixed(2)}`);

                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: rows
                                });
                            },
                            error: function(xhr) {
                                console.log('Error loading expenses:', xhr.responseText);
                                $('#grandTotal').text('₹ 0.00');
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
                            data: 'user_name',
                            render: data => data ?? 'N/A'
                        },
                        {
                            data: 'date_time',
                            render: data => data ?? 'N/A'
                        },
                        {
                            data: 'amount',
                            className: 'd-none d-md-table-cell',
                            render: data => `₹ ${parseFloat(data || 0).toFixed(2)}`
                        },
                        {
                            data: 'service',
                            className: 'd-none d-md-table-cell',
                            render: data => ucfirst(data)
                        },
                        {
                            data: 'id',
                            className: 'd-none d-md-table-cell',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                let actions = '<div class="icon">';

                                @if (app('hasPermission')(33, 'update'))
                                    actions +=
                                        `<i class="fa fa-pencil m-r-5 icon1 edit-expense" style="cursor:pointer" data-id="${data}" title="Edit"></i>`;
                                @endif
                                @if (app('hasPermission')(33, 'delete'))
                                    actions +=
                                        `<i class="fa fa-trash-o m-r-5 icon2 delete-expense" style="cursor:pointer" data-id="${data}" title="Delete"></i>`;
                                @endif
                                actions += '</div>';
                                return actions;
                            }
                        },
                        {
                            data: null,
                            className: 'd-table-cell d-md-none text-center',
                            orderable: false,
                            searchable: false,
                            render: function() {
                                return `<button class="btn btn-link expand-btn"><i class="fa fa-chevron-down"></i></button>`;
                            }
                        }
                    ],
                    paging: true,
                    searching: true,
                    ordering: true,
                    order: [[1, 'desc']]
                });
                window.expenseTable = expenseTable;
            }, 50);
        }

        function reloadExpenseTable() {
            if (!expenseTable) {
                initExpenseTable();
                return;
            }
            expenseTable.ajax.reload(null, false);
        }

        initExpenseTable();

        // Filters
        $('#filterMonth, #filterYear, #filterStaff').off('change').on('change', function() {
            reloadExpenseTable();
        });

        $(document).on('click', '.delete-expense', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this expense?",
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
                        url: `/api/expenses/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            reloadExpenseTable();
                        },
                        error: function(xhr) {
                            let errorMsg = 'Failed to delete expense.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.view-expense', function() {
            let id = $(this).data('id');
            window.location.href = `/expense/show/${id}`;
        });

        $(document).on('click', '.edit-expense', function() {
            let id = $(this).data('id');
            window.location.href = `/expense/edit/${id}`;
        });
    });

    // Mobile expand
    $(document).on("click", ".expand-btn", function(e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");
        const existingRow = tr.next(".details-row");

        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        if (!window.expenseTable || !$.fn.DataTable.isDataTable('#expensestbl')) {
            return;
        }

        const rowData = window.expenseTable.row(tr).data() || {};

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="5">
                    <div class="details-content">
                        <div><strong>Amount:</strong> ₹ ${parseFloat(rowData.amount || 0).toFixed(2)}</div>
                        <div><strong>Service:</strong> ${rowData.service ?? 'N/A'}</div>
                        <div class="mt-2">
                            <strong>Actions:</strong>
                            <div class="icon mt-1">
                                @if (app('hasPermission')(33, 'update'))
                                    <i class="fa fa-pencil m-r-5 icon1 edit-expense" data-id="${rowData.id}" title="Edit"></i>
                                @endif
                                @if (app('hasPermission')(33, 'delete'))
                                    <i class="fa fa-trash-o m-r-5 icon2 delete-expense" data-id="${rowData.id}" title="Delete"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });
</script> --}}
    <script>
    $('#filterMonth').select2({
        placeholder: "Select Month",
        width: '100%'
    });
    $('#filterMonth').on('select2:open', function() {
        $('.select2-search__field').attr('placeholder', 'Search Month');
    });
    $('#filterYear').select2({
        placeholder: "Select Year",
        width: '100%'
    });
    $('#filterYear').on('select2:open', function() {
        $('.select2-search__field').attr('placeholder', 'Search Year');
    });

    $(document).on('click', '#exportButton', function() {
        let branchId = localStorage.getItem("selectedBranchId");

        if (!branchId) {
            alert("Please select a branch first.");
            return;
        }

        window.location.href = "{{ route('expense.export') }}" + "?branch_id=" + branchId;
    });

    // Load filter options
    $.ajax({
        url: '/api/expenses/filters',
        type: 'GET',
        success: function(res) {
            // Year
            $('#filterYear').empty().append('<option value="">All Years</option>');
            res.years.forEach(year => {
                $('#filterYear').append(`<option value="${year}">${year}</option>`);
            });

            // Staff
            $('#filterStaff').empty().append('<option value="">All Staff</option>');
            res.staff.forEach(s => {
                $('#filterStaff').append(`<option value="${s.id}">${s.name}</option>`);F
            });
        }
    });

    $(document).ready(function() {
        let branchId = localStorage.getItem('selectedBranchId');
        let expenseTable;

        function ucfirst(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : 'N/A';
        }

        function initExpenseTable() {
            if ($.fn.DataTable.isDataTable('#expensestbl')) {
                try {
                    let existingTable = $('#expensestbl').DataTable();
                    existingTable.destroy();
                    $('#expensestbl').removeClass('dataTable');
                    $('#expensestbl tbody').empty();
                    $.removeData($('#expensestbl')[0], 'DataTable');
                    $.removeData($('#expensestbl')[0], 'DataTables_DataTable');
                    $('#expensestbl').off();
                } catch (e) {
                    $('#expensestbl tbody').empty();
                    $.removeData($('#expensestbl')[0]);
                }
            }

            if ($('#expensestbl tbody').length === 0) {
                $('#expensestbl').append('<tbody id="expenseTableBody"></tbody>');
            }

            setTimeout(function() {
                expenseTable = $('#expensestbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/expenses',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                branch_id: branchId,
                                month: $('#filterMonth').val(),
                                year: $('#filterYear').val(),
                                user_id: $('#filterStaff').val(),
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            success: function(json) {
                                const rows = json.data || [];
                                const grandTotal = rows.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
                                $('#grandTotal').text(`₹ ${grandTotal.toFixed(2)}`);

                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: rows
                                });
                            },
                            error: function(xhr) {
                                console.log('Error loading expenses:', xhr.responseText);
                                $('#grandTotal').text('₹ 0.00');
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
                            data: 'user_name',
                            render: data => data ?? 'N/A'
                        },
                        {
                            data: 'date_time',
                            render: data => data ?? 'N/A'
                        },
                        {
                            data: 'amount',
                            className: 'd-none d-md-table-cell',
                            render: data => `₹ ${parseFloat(data || 0).toFixed(2)}`
                        },
                        {
                            data: 'service',
                            className: 'd-none d-md-table-cell',
                            render: data => ucfirst(data)
                        },
                        {
                            data: 'id',
                            className: 'd-none d-md-table-cell',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                let actions = '<div class="icon">';

                                @if (app('hasPermission')(33, 'update'))
                                    actions +=
                                        `<i class="fa fa-pencil m-r-5 icon1 edit-expense" style="cursor:pointer" data-id="${data}" title="Edit"></i>`;
                                @endif
                                @if (app('hasPermission')(33, 'delete'))
                                    actions +=
                                        `<i class="fa fa-trash-o m-r-5 icon2 delete-expense" style="cursor:pointer" data-id="${data}" title="Delete"></i>`;
                                @endif
                                actions += '</div>';
                                return actions;
                            }
                        },
                        {
                            data: null,
                            className: 'd-table-cell d-md-none text-center',
                            orderable: false,
                            searchable: false,
                            render: function() {
                                return `<button class="btn btn-link expand-btn"><i class="fa fa-chevron-down"></i></button>`;
                            }
                        }
                    ],
                    paging: true,
                    searching: true,
                    ordering: true,
                    order: [[1, 'desc']]
                });
                window.expenseTable = expenseTable;
            }, 50);
        }

        function reloadExpenseTable() {
            if (!expenseTable) {
                initExpenseTable();
                return;
            }
            expenseTable.ajax.reload(null, false);
        }

        initExpenseTable();

        // Filters
        $('#filterMonth, #filterYear, #filterStaff').off('change').on('change', function() {
            reloadExpenseTable();
        });

        $(document).on('click', '.delete-expense', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this expense?",
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
                        url: `/api/expenses/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            reloadExpenseTable();
                        },
                        error: function(xhr) {
                            let errorMsg = 'Failed to delete expense.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.view-expense', function() {
            let id = $(this).data('id');
            window.location.href = `/expense/show/${id}`;
        });

        $(document).on('click', '.edit-expense', function() {
            let id = $(this).data('id');
            window.location.href = `/expense/edit/${id}`;
        });
    });

    // Mobile expand
    $(document).on("click", ".expand-btn", function(e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");
        const existingRow = tr.next(".details-row");

        if (existingRow.length) {
            existingRow.slideToggle(300);
            icon.toggleClass("fa-chevron-down fa-chevron-up");
            return;
        }

        if (!window.expenseTable || !$.fn.DataTable.isDataTable('#expensestbl')) {
            return;
        }

        const rowData = window.expenseTable.row(tr).data() || {};

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="5">
                    <div class="details-content">
                        <div><strong>Amount:</strong> ₹ ${parseFloat(rowData.amount || 0).toFixed(2)}</div>
                        <div><strong>Service:</strong> ${rowData.service ?? 'N/A'}</div>
                        <div class="mt-2">
                            <strong>Actions:</strong>
                            <div class="icon mt-1">
                                @if (app('hasPermission')(33, 'update'))
                                    <i class="fa fa-pencil m-r-5 icon1 edit-expense" data-id="${rowData.id}" title="Edit"></i>
                                @endif
                                @if (app('hasPermission')(33, 'delete'))
                                    <i class="fa fa-trash-o m-r-5 icon2 delete-expense" data-id="${rowData.id}" title="Delete"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.toggleClass("fa-chevron-down fa-chevron-up");
    });
</script>

@endsection


