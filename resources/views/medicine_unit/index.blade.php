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

    .custom-close {
        background-color: #f5b6a5 !important;
        opacity: 1;
        border: 1px solid #f5b6a5;
        border-radius: 5px;
        padding: 3px 6px;
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
                            <h3 class="card-title d-inline-block text-white"><i class="fa fa-medkit "
                                    style="font-size:20px;margin-right:10px"></i>All Medicine Units </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>
                            <button class="btn btn-rounded btn-hdr" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                                <i class="fa fa-plus"></i> Add Unit
                            </button>
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="medicineunittbl" class="table custom-table">
                                    <thead style="background-color:#ff8e6;" class="text-center">
                                        <tr>
                                            <th>Unit</th>
                                            <th class="d-none d-md-table-cell">Created At</th>
                                            <th class="d-none d-md-table-cell">Action</th>
                                            <th class="d-table-cell d-md-none">Details</th> <!-- ✅ For mobile -->
                                        </tr>
                                    </thead>
                                    <tbody class="medicineunittbl">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Unit Modal -->
        <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="addUnitForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="addUnitModalLabel">Add Medicine Unit</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="unit" class="form-label">Unit Name <span
                                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unit" name="unit" required placeholder="e.g. mg, ml, Tablet">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Unit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Unit Modal -->
        <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editUnitForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_unit_id" name="id">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="editUnitModalLabel">Edit Medicine Unit</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_unit" class="form-label">Unit Name <span
                                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_unit" name="unit" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Unit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '#exportButton', function() {
            let branchId = localStorage.getItem('selectedBranchId');
            if (!branchId) {
                alert("Please select a branch first.");
                return;
            }
            window.location.href = "{{ route('medicine-units.export') }}" + "?branch_id=" + branchId; // Assuming reuse of medicine export or similar
        });

        $(document).ready(function() {
            fetchUnits();

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function fetchUnits() {
                let branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: "/api/medicine-units",
                    type: "GET",
                    data: {
                        branch_id: branchId
                    },
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        if ($.fn.DataTable.isDataTable("#medicineunittbl")) {
                            $('#medicineunittbl').DataTable().clear().destroy();
                        }

                        let tableBody = $(".medicineunittbl");
                        tableBody.empty();

                        if (response.status && Array.isArray(response.data)) {
                            response.data.forEach((item) => {
                                let createdAt = item.created_at ? new Date(item.created_at).toLocaleDateString() : 'N/A';
                                
                                let row = `
                                    <tr class="text-center">
                                        <td>${ucfirst(item.unit)}</td>
                                        <td class="d-none d-md-table-cell">${createdAt}</td>
                                        <td class="d-none d-md-table-cell">
                                            <div class="icon" style="cursor:pointer">
                                                <i class="fa fa-pencil m-r-5 icon1 edit-unit" data-id="${item.id}" data-unit="${item.unit}" title="Edit Unit"></i> 
                                                <i class="fa fa-trash-o m-r-5 icon2 delete-unit" data-id="${item.id}" title="Delete Unit"></i> 
                                            </div>
                                        </td>
                                        <!-- ✅ Mobile only -->
                                        <td class="d-table-cell d-md-none text-center">
                                            <button class="btn btn-link expand-btn"><i class="fa fa-chevron-down"></i></button>
                                        </td>
                                    </tr>`;
                                tableBody.append(row);
                            });
                        }

                        $('#medicineunittbl').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            responsive: true,
                            autoWidth: false
                        });
                    },
                    error: function(error) {
                        console.error("Error fetching medicine units:", error);
                    }
                });
            }

            // Add Unit
            $('#addUnitForm').on('submit', function(e) {
                e.preventDefault();
                let branchId = localStorage.getItem('selectedBranchId');
                if (!branchId) {
                    Swal.fire('Error', 'Please select a branch first.', 'error');
                    return;
                }
                $.ajax({
                    url: "/api/medicine-units",
                    type: "POST",
                    data: {
                        unit: $('#unit').val(),
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        Swal.fire('Success', 'Unit added successfully!', 'success');
                        $('#addUnitModal').modal('hide');
                        $('#addUnitForm')[0].reset();
                        fetchUnits();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to add unit', 'error');
                    }
                });
            });

            // Edit Unit
            $(document).on('click', '.edit-unit', function() {
                let id = $(this).data('id');
                let unit = $(this).data('unit');
                $('#edit_unit_id').val(id);
                $('#edit_unit').val(unit);
                $('#editUnitModal').modal('show');
            });

            $('#editUnitForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_unit_id').val();
                let branchId = localStorage.getItem('selectedBranchId');
                $.ajax({
                    url: "/api/medicine-units/" + id,
                    type: "PUT",
                    data: {
                        unit: $('#edit_unit').val(),
                        branch_id: branchId
                    },
                    headers: {
                        "Authorization": "Bearer " + token
                    },
                    success: function(response) {
                        Swal.fire('Success', 'Unit updated successfully!', 'success');
                        $('#editUnitModal').modal('hide');
                        fetchUnits();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to update unit', 'error');
                    }
                });
            });

            // Delete Unit
            $(document).on('click', '.delete-unit', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
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
                            url: "/api/medicine-units/" + id,
                            type: "DELETE",
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', 'Unit has been deleted.', 'success');
                                fetchUnits();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Failed to delete unit', 'error');
                            }
                        });
                    }
                });
            });
        });

        // ✅ Mobile Details Expand
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

            const createdAt = tr.find("td").eq(1).text() || "N/A";
            const actionHtml = tr.find("td").eq(2).html() || "";

            const detailsRow = $(`
                <tr class="details-row">
                    <td colspan="3">
                        <div class="details-content">
                            <div><strong>Created At:</strong> ${createdAt}</div>
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
