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

    colgroup {
        display: none;
    }

    .details-content {
        padding: 10px;
        border-left: 3px solid #f89884;
        margin-left: 10px;
    }

    button.expand-btn {
        background: #f89884 !important;
        color: white !important;
        border: none !important;
        padding: 8px !important;
        border-radius: 10px !important;
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
                                <i class="fa fa-cogs px-2" style="font-size:20px"></i>All Inventory
                            </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportInventoryBtn">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span> 
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="inventorytbl" class="table custom-table">
                                    <thead style="background-color:#ff8e29;" class="text-center">
                                        <tr>
                                            <th>Medicine</th>
                                            <th class="d-none d-md-table-cell">Category</th>
                                            <th class="d-none d-md-table-cell">Batch Number</th>
                                            <th class="d-none d-md-table-cell">Quantity</th>
                                            <th class="d-none d-md-table-cell">Price</th>
                                            <th class="d-none d-md-table-cell">Expiry Date</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="inventoryBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Quantity Modal -->
        <div class="modal fade" id="updateQuantityModal" tabindex="-1" role="dialog" aria-labelledby="updateQuantityModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #f89884; color: white;">
                        <h5 class="modal-title" id="updateQuantityModalLabel">Update Medicine Quantity</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="updateQuantityForm">
                        <div class="modal-body">
                            <input type="hidden" id="updateMedicineId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineNameDisplay">Medicine</label>
                                        <input type="text" class="form-control" id="medicineNameDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineCategoryDisplay">Category</label>
                                        <input type="text" class="form-control" id="medicineCategoryDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineBatchDisplay">Batch Number</label>
                                        <input type="text" class="form-control" id="medicineBatchDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicineExpiryDisplay">Expiry Date</label>
                                        <input type="text" class="form-control" id="medicineExpiryDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currentQuantityDisplay">Current Quantity</label>
                                        <input type="text" class="form-control" id="currentQuantityDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medicinePriceDisplay">Price</label>
                                        <input type="text" class="form-control" id="medicinePriceDisplay" readonly>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Stock Update</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="stockAction" id="addStock" value="add" checked>
                                    <label class="form-check-label" for="addStock">Add Quantity</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="stockAction" id="minusStock" value="minus">
                                    <label class="form-check-label" for="minusStock">Minus Quantity</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label id="adjustmentLabel">How much to Add?</label>
                                <input type="number" class="form-control" id="adjustmentQuantity" min="1" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 50px;">Cancel</button>
                            <button type="submit" class="btn btn-primary" style="border-radius: 50px;">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script>
        $(document).on('click', '#exportInventoryBtn', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Branch ID not found in localStorage!");
                return;
            }
            window.location.href = "{{ route('inventory.export') }}" + "?branch_id=" + branchId;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle_btn');
            const sidebar = document.querySelector('.sidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mini-sidebar');
                });
            }
        });

        $(document).ready(function() {
            let branchId = localStorage.getItem('selectedBranchId');
            let token = sessionStorage.getItem('token');
            let userId = sessionStorage.getItem('user_id');
            let inventoryTable;

            function formatDate(value) {
                if (!value) return 'N/A';
                let d = new Date(value);
                return isNaN(d.getTime()) ? 'N/A' : d.toLocaleDateString();
            }

            function initInventoryTable() {
                if ($.fn.DataTable.isDataTable("#inventorytbl")) {
                    try {
                        let existingTable = $('#inventorytbl').DataTable();
                        existingTable.destroy();
                        $('#inventorytbl').removeClass('dataTable');
                        $('#inventorytbl tbody').empty();
                        $.removeData($('#inventorytbl')[0], 'DataTable');
                        $.removeData($('#inventorytbl')[0], 'DataTables_DataTable');
                        $('#inventorytbl').off();
                    } catch (e) {
                        $('#inventorytbl tbody').empty();
                        $.removeData($('#inventorytbl')[0]);
                    }
                }

                if ($('#inventorytbl tbody').length === 0) {
                    $('#inventorytbl').append('<tbody id="inventoryBody"></tbody>');
                }

                setTimeout(function () {
                    inventoryTable = $('#inventorytbl').DataTable({
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
                                url: "{{ url('/api/medicines') }}",
                                type: "GET",
                                dataType: "json",
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: perPage,
                                    search: data.search?.value || ''
                                },
                                headers: {
                                    "Authorization": "Bearer " + token
                                },
                                success: function (response) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: response.pagination?.total || response.recordsTotal || 0,
                                        recordsFiltered: response.pagination?.total || response.recordsFiltered || 0,
                                        data: response.data || []
                                    });
                                },
                                error: function (xhr) {
                                    console.log("Fetch error:", xhr.responseText);
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
                                data: "name",
                                render: function (data) { return data ?? 'N/A'; }
                            },
                            {
                                data: "category_name",
                                render: function (data) { return data ?? 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "batch_no",
                                render: function (data) { return data ?? 'N/A'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "quantity",
                                render: function (data) { return data ?? '0'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "unit",
                                render: function (data) { return data ?? '0'; },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: "expiry_date",
                                render: function (data) { return formatDate(data); },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: null,
                                render: function (data, type, row) {
                                    return `
                                        <button class="btn btn-primary btn-sm update-quantity-btn" 
                                            data-id="${row.id}" 
                                            data-name="${row.name}" 
                                            data-category="${row.category_name ?? 'N/A'}"
                                            data-batch="${row.batch_no ?? 'N/A'}"
                                            data-quantity="${row.quantity ?? '0'}"
                                            data-price="${row.unit ?? '0'}"
                                            data-expiry="${row.expiry_date ?? 'N/A'}"
                                            style="border-radius: 50px;">
                                            <i class="fa fa-plus"></i> Add/edit Stock
                                        </button>
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

            initInventoryTable();

            // Open Update Quantity Modal
            $(document).on('click', '.update-quantity-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let category = $(this).data('category');
                let batch = $(this).data('batch');
                let quantity = $(this).data('quantity');
                let price = $(this).data('price');
                let expiry = $(this).data('expiry');

                $('#updateMedicineId').val(id);
                $('#medicineNameDisplay').val(name);
                $('#medicineCategoryDisplay').val(category);
                $('#medicineBatchDisplay').val(batch);
                $('#currentQuantityDisplay').val(quantity);
                $('#medicinePriceDisplay').val(price);
                $('#medicineExpiryDisplay').val(expiry !== 'N/A' ? new Date(expiry).toLocaleDateString() : 'N/A');
                
                $('#adjustmentQuantity').val('');
                $('#addStock').prop('checked', true);
                $('#adjustmentLabel').text('How much to Add?');
                
                $('#updateQuantityModal').modal('show');
            });

            // Expand Button (Mobile)
            $(document).on('click', '.expand-btn', function() {
                let btn = $(this);
                let icon = btn.find('i');
                let tr = btn.closest('tr');

                if (!$.fn.DataTable.isDataTable('#inventorytbl')) {
                    return;
                }

                let table = $('#inventorytbl').DataTable();
                let row = table.row(tr);
                let data = row.data();
                if (!data) return;

                let nextRow = tr.next('.details-row');
                if (nextRow.length) {
                    nextRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let category = data.category_name ?? 'N/A';
                let batch = data.batch_no ?? 'N/A';
                let quantity = data.quantity ?? '0';
                let price = data.unit ?? '0';
                let expiry = formatDate(data.expiry_date);
                let actionBtn = `
                    <button class="btn btn-primary btn-sm update-quantity-btn" 
                        data-id="${data.id}" 
                        data-name="${data.name}" 
                        data-category="${data.category_name ?? 'N/A'}"
                        data-batch="${data.batch_no ?? 'N/A'}"
                        data-quantity="${data.quantity ?? '0'}"
                        data-price="${data.unit ?? '0'}"
                        data-expiry="${data.expiry_date ?? 'N/A'}"
                        style="border-radius: 50px;">
                        <i class="fa fa-plus"></i> Add/edit Stock
                    </button>
                `;

                let detailsRow = `
                    <tr class="details-row">
                        <td colspan="8">
                            <div class="details-content">
                                <div><strong>Category:</strong> ${category}</div>
                                <div><strong>Batch Number:</strong> ${batch}</div>
                                <div><strong>Quantity:</strong> ${quantity}</div>
                                <div><strong>Price:</strong> ${price}</div>
                                <div><strong>Expiry Date:</strong> ${expiry}</div>
                                <div class="mt-2"><strong>Action:</strong><br>${actionBtn}</div>
                            </div>
                        </td>
                    </tr>
                `;

                tr.after(detailsRow);
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $(window).on('resize', function() {
                if ($(window).width() >= 768) {
                    $('.details-row').remove();
                    $('.expand-btn i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });

            // Handle Stock Action Radio Button Change
            $(document).on('change', 'input[name="stockAction"]', function() {
                if ($(this).val() === 'add') {
                    $('#adjustmentLabel').text('How much to Add?');
                } else {
                    $('#adjustmentLabel').text('How much to Minus?');
                }
            });

            // Handle Quantity Update Form Submission
            $('#updateQuantityForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#updateMedicineId').val();
                let currentQty = parseInt($('#currentQuantityDisplay').val());
                let adjustment = parseInt($('#adjustmentQuantity').val());
                let action = $('input[name="stockAction"]:checked').val();
                
                let newQuantity = action === 'add' ? currentQty + adjustment : currentQty - adjustment;
                
                if (newQuantity < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Quantity',
                        text: 'Stock cannot be negative.'
                    });
                    return;
                }

                $.ajax({
                    url: `/api/medicines/${id}/update-quantity`,
                    type: "POST",
                    dataType: "json",
                    data: {
                        quantity: newQuantity
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#updateQuantityModal').modal('hide');
                            if (inventoryTable) {
                                inventoryTable.ajax.reload(null, false);
                            } else {
                                initInventoryTable();
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error("Update error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update quantity.'
                        });
                    }
                });
            });
        });
    </script>
@endsection
