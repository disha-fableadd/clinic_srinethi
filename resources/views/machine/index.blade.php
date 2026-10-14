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

    #followuptbl thead th {
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
                                <i class="fa fa-cogs px-2" style="font-size:20px"></i> All Machines
                            </h3>
                            @if (app('hasPermission')(8, 'create'))
                                <a href="" class="btn btn-rounded btn-hdr" data-bs-toggle="modal"
                                    data-bs-target="#addMachineModal">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="machinetbl" class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="d-none d-md-table-cell">Price</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
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

        <!-- Add Machine Modal -->
        <div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="machineForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="addMachineModalLabel">Add Machine</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="machineName" class="form-label">Title <span
                                        class="text-danger">*</span> <span class="text-danger error-msg d-none"
                                        style="font-size: 12px; font-weight: normal;">(Title filed is required)</span></label>
                                <input type="text" class="form-control" id="machineName" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="machinedescription" class="form-label">Description</label>
                                <textarea class="form-control" id="machinedescription" name="description" rows="3"
                                    style="border-radius:10px"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="machineName" class="form-label">Price <span
                                        class="text-danger">*</span> <span class="text-danger error-msg d-none"
                                        style="font-size: 12px; font-weight: normal;">(Price Filed is required)</span></label>
                                <input type="number" class="form-control" id="price" name="price">
                            </div>
                            <div id="machineSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="machineError" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Machine</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Machine Modal -->
        <div class="modal fade" id="editMachineModal" tabindex="-1" aria-labelledby="editMachineModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="editMachineForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editMachineId" name="id">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="editMachineModalLabel">Edit Machine</h5>
                            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button> --}}
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editMachineName" class="form-label">Title <span
                                        class="text-danger">*</span> <span class="text-danger error-msg d-none"
                                        style="font-size: 12px; font-weight: normal;">(Required)</span></label>
                                <input type="text" class="form-control" id="editMachineName" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="editMachinedescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editMachinedescription" name="description" rows="3"
                                    style="border-radius:10px"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editMachineName" class="form-label">Price <span
                                        class="text-danger">*</span> <span class="text-danger error-msg d-none"
                                        style="font-size: 12px; font-weight: normal;">(Required)</span></label>
                                <input type="number" class="form-control" id="editprice" name="price" >
                            </div>
                            <div id="editMachineSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="editMachineError" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Machine</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
    $(document).ready(function() {
        let branchId = localStorage.getItem('selectedBranchId');
        const token = localStorage.getItem('token');
        let machineTable;

        function setFieldValidity($field, isValid) {
            $field.toggleClass('is-invalid', !isValid);
            $field.siblings('label').find('.error-msg').toggleClass('d-none', isValid);
        }

        function validateAddForm() {
            const nameVal = $('#machineName').val().trim();
            const priceVal = $('#price').val().trim();
            const isNameValid = nameVal.length > 0;
            const isPriceValid = priceVal.length > 0;
            setFieldValidity($('#machineName'), isNameValid);
            setFieldValidity($('#price'), isPriceValid);
            return isNameValid && isPriceValid;
        }

        function validateEditForm() {
            const nameVal = $('#editMachineName').val().trim();
            const priceVal = $('#editprice').val().trim();
            const isNameValid = nameVal.length > 0;
            const isPriceValid = priceVal.length > 0;
            setFieldValidity($('#editMachineName'), isNameValid);
            setFieldValidity($('#editprice'), isPriceValid);
            return isNameValid && isPriceValid;
        }

        $('#machineName, #price').on('input blur', function() {
            validateAddForm();
        });

        $('#editMachineName, #editprice').on('input blur', function() {
            validateEditForm();
        });

        function initMachineTable() {
            if ($.fn.DataTable.isDataTable('#machinetbl')) {
                try {
                    let existingTable = $('#machinetbl').DataTable();
                    existingTable.destroy();
                    $('#machinetbl').removeClass('dataTable');
                    $('#machinetbl tbody').empty();
                    $.removeData($('#machinetbl')[0], 'DataTable');
                    $.removeData($('#machinetbl')[0], 'DataTables_DataTable');
                    $('#machinetbl').off();
                } catch (e) {
                    $('#machinetbl tbody').empty();
                    $.removeData($('#machinetbl')[0]);
                }
            }

            if ($('#machinetbl tbody').length === 0) {
                $('#machinetbl').append('<tbody></tbody>');
            }

            setTimeout(function() {
                machineTable = $('#machinetbl').DataTable({
                    processing: true,
                    serverSide: true,
                    retrieve: true,
                    destroy: true,
                    ajax: function(data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;

                        $.ajax({
                            url: '/api/machines',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                branch_id: branchId,
                                page: page,
                                per_page: data.length,
                                search: data.search?.value || ''
                            },
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(json) {
                                callback({
                                    draw: data.draw,
                                    recordsTotal: json.pagination?.total || 0,
                                    recordsFiltered: json.pagination?.total || 0,
                                    data: json.machines || []
                                });
                            },
                            error: function(xhr) {
                                console.error('Failed to load machines', xhr.responseText);
                                callback({
                                    draw: data.draw,
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                            }
                        });
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [3, 4]
                    }],
                    columns: [{
                            data: 'name',
                            defaultContent: 'N/A'
                        },
                        {
                            data: 'description',
                            defaultContent: 'N/A'
                        },
                        {
                            data: 'price',
                            defaultContent: 'N/A',
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                                    <div class="icon" style="cursor:pointer">
                                        @if (app('hasPermission')(8, 'update'))
                                            <i class="fa fa-pencil m-r-5 edit-machine icon1" data-id="${row.id}" title="Edit"></i>
                                        @endif
                                        @if (app('hasPermission')(8, 'delete'))
                                            <i class="fa fa-trash-o m-r-5 delete-machine icon2" data-id="${row.id}" title="Delete"></i>
                                        @endif
                                    </div>
                                `;
                            },
                            className: 'd-none d-md-table-cell'
                        },
                        {
                            data: null,
                            render: function() {
                                return `
                                    <button class="btn btn-link expand-btn d-table-cell d-md-none">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
                                `;
                            },
                            orderable: false,
                            className: 'text-center d-table-cell d-md-none'
                        }
                    ],
                    order: [
                        [0, 'asc']
                    ],
                    pageLength: 10
                });
                window.machineTable = machineTable;
            }, 50);
        }

        function loadMachines() {
            if (!machineTable) {
                initMachineTable();
                return;
            }
            machineTable.ajax.reload(null, false);
        }

        initMachineTable();

        // Add
        $('#machineForm').on('submit', function(e) {
            e.preventDefault();
            $('#machineSuccess,#machineError').hide();
            if (!validateAddForm()) {
                return;
            }
            $.ajax({
                url: "{{ route('machines.store') }}",
                method: "POST",
                data: {
                    name: $('#machineName').val(),
                    description: $('#machinedescription').val(),
                    price: $('#price').val(),
                    branch_id: branchId
                },
                success: function(response) {
                    $('#machineSuccess').text(response.message).fadeIn();
                    setTimeout(() => {
                        $('#addMachineModal').modal('hide');
                         location.reload(); // 🔥 page refresh
                        // loadMachines();
                    }, 1000);
                },
                error: function(xhr) {
                    $('#machineError').text(xhr.responseJSON?.message ||
                        'Something went wrong!').fadeIn();
                    setTimeout(() => $('#machineError').fadeOut(), 3000);
                }
            });
        });

        // Edit
        $(document).on('click', '.edit-machine', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '/api/machines/' + id,
                type: 'GET',
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(machine) {
                    $('#editMachineId').val(machine.id);
                    $('#editMachineName').val(machine.name);
                    $('#editprice').val(machine.price);
                    $('#editMachinedescription').val(machine.description);
                    $('#editMachineModal').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch machine data.', 'error');
                }
            });
        });

        // Update
        $('#editMachineForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#editMachineId').val();
            $('#editMachineSuccess,#editMachineError').hide();
            if (!validateEditForm()) {
                return;
            }
            $.ajax({
                url: '/api/machines/' + id,
                type: 'PUT',
                data: {
                    name: $('#editMachineName').val(),
                    description: $('#editMachinedescription').val(),
                    price: $('#editprice').val(),
                    branch_id: branchId
                },
                headers: {
                    "Authorization": "Bearer " + token
                },
                success: function(response) {
                    $('#editMachineSuccess').text(response.message).fadeIn();
                    setTimeout(() => {
                        $('#editMachineModal').modal('hide');
                        $('#editMachineName, #editprice').removeClass('is-invalid');
                        loadMachines();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#editMachineError').text(xhr.responseJSON?.message ||
                        'Something went wrong!').fadeIn();
                    setTimeout(() => $('#editMachineError').fadeOut(), 3000);
                }
            });
        });

        // Delete
        $(document).on('click', '.delete-machine', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this machine!",
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
                        url: '/api/machines/' + id,
                        type: 'DELETE',
                        headers: {
                            "Authorization": "Bearer " + token
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Machine deleted successfully!',
                                'success');
                            loadMachines();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Failed to delete machine.', 'error');
                        }
                    });
                }
            });
        });

    });

    // === Mobile dropdown expand (Details button) ===
    $(document).on("click", ".expand-btn", function(e) {
        e.stopPropagation();
        const btn = $(this);
        const icon = btn.find("i");
        const tr = btn.closest("tr");
        const existingRow = tr.next(".details-row");

        // Close existing
        $(".details-row").not(existingRow).remove();
        $(".expand-btn i").removeClass("fa-chevron-up").addClass("fa-chevron-down");

        // Toggle if already open
        if (existingRow.length) {
            existingRow.remove();
            return;
        }

        if (!window.machineTable || !$.fn.DataTable.isDataTable('#machinetbl')) {
            return;
        }

        const row = window.machineTable.row(tr);
        const data = row.data() || {};

        const description = data.description || "N/A";
        const price = data.price ?? "N/A";

        const actionHtml = `
            <div class="icon" style="cursor:pointer">
                @if (app('hasPermission')(8, 'update'))
                    <i class="fa fa-pencil m-r-5 edit-machine icon1" data-id="${data.id}" title="Edit"></i>
                @endif
                @if (app('hasPermission')(8, 'delete'))
                    <i class="fa fa-trash-o m-r-5 delete-machine icon2" data-id="${data.id}" title="Delete"></i>
                @endif
            </div>
        `;

        const detailsRow = $(`
            <tr class="details-row">
                <td colspan="5">
                    <div class="details-content">
                        <div><strong>Description:</strong> ${description}</div>
                        <div><strong>Price:</strong> ${price}</div>
                        <div class="mt-2"><strong>Action:</strong> ${actionHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailsRow);
        icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
    });
</script>
@endsection


