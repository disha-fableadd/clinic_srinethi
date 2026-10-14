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
      .card-header{
        background-color:#f89884 !important; 
    }
    .btn-rounded{
        background-color: #fed9cf !important;
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
                            <i class="fa fa-capsules" style="font-size:20px"></i> Code Master
                        </h3>

                        <a href="" class="btn btn-rounded btn-hdr" data-bs-toggle="modal"
                           data-bs-target="#addCodeModal">
                            <i class="fa fa-plus"></i> Add Code
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="codetbl" class="table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="codeTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Code Modal -->
    <div class="modal fade" id="addCodeModal" tabindex="-1" aria-labelledby="addCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="codeForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                        <h5 class="modal-title" id="addCodeModalLabel">Add New Code</h5>
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Code <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                        <div id="globalSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="globalError" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Code</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Code Modal -->
    <div class="modal fade" id="editCodeModal" tabindex="-1" aria-labelledby="editCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editCodeForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCodeId" name="id">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                        <h5 class="modal-title" id="editCodeModalLabel">Edit Code</h5>
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Code <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editCode" name="code" required>
                        </div>
                        <div id="editGlobalSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="editGlobalError" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Code</button>
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

    let codeTable = $('#codetbl').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        "columnDefs": [{ "orderable": false, "targets": 1 }]
    });

    function loadCodes() {
        if ($.fn.DataTable.isDataTable('#codetbl')) {
            codeTable.destroy();
        }

        $.ajax({
            url: '/api/codemaster',
            type: 'GET',
            data: { branch_id: branchId },
            headers: { "Authorization": "Bearer " + token },
            success: function(data) {
                codeTable = $('#codetbl').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    "columnDefs": [{ "orderable": false, "targets": 1 }]
                });
                codeTable.clear();
                data.forEach(function(item) {
                    codeTable.row.add([
                        item.code || 'N/A',
                        `<div class="icon" style="cursor:pointer">
                            <i class="fa fa-pencil m-r-5 icon1 edit-code" data-id="${item.id}" title="Edit"></i>
                            <i class="fa fa-trash-o m-r-5 icon2 delete-code" data-id="${item.id}" title="Delete"></i>
                        </div>`
                    ]).draw(false);
                });
            }
        });
    }

    loadCodes();

    $('#codeForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serializeArray();
        formData.push({name: 'branch_id', value: branchId});

        $.ajax({
            url: "/api/codemaster",
            method: "POST",
            data: $.param(formData),
            headers: { "Authorization": "Bearer " + token },
            success: function(response) {
                $('#globalSuccess').text(response.message).fadeIn();
                setTimeout(() => {
                    $('#addCodeModal').modal('hide');
                    $('#codeForm')[0].reset();
                    $('#globalSuccess').fadeOut();
                    loadCodes();
                }, 1500);
            },
            error: function(xhr) {
                $('#globalError').text(xhr.responseJSON?.message || 'Error saving code').fadeIn();
                setTimeout(() => $('#globalError').fadeOut(), 3000);
            }
        });
    });

    $(document).on('click', '.edit-code', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '/api/codemaster/' + id,
            type: 'GET',
            headers: { "Authorization": "Bearer " + token },
            success: function(data) {
                $('#editCodeId').val(data.id);
                $('#editCode').val(data.code);
                $('#editCodeModal').modal('show');
            }
        });
    });

    $('#editCodeForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editCodeId').val();
        $.ajax({
            url: '/api/codemaster/' + id,
            type: 'PUT',
            data: $(this).serialize(),
            headers: { "Authorization": "Bearer " + token },
            success: function(response) {
                $('#editGlobalSuccess').text(response.message).fadeIn();
                setTimeout(() => {
                    $('#editCodeModal').modal('hide');
                    loadCodes();
                }, 1500);
            }
        });
    });

    $(document).on('click', '.delete-code', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cfece0',
            cancelButtonColor: '#f89884',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/codemaster/' + id,
                    type: 'DELETE',
                    headers: { "Authorization": "Bearer " + token },
                    success: function() {
                        Swal.fire('Deleted!', '', 'success');
                        loadCodes();
                    }
                });
            }
        });
    });
});
</script>
@endsection
