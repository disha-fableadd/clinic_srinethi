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
                            <i class="fa fa-calendar-check-o px-2" style="font-size:20px"></i> All Symptom
                        </h3>

                        @if (app('hasPermission')(7, 'create'))
                            <a href="" class="btn btn-rounded btn-hdr" data-bs-toggle="modal"
                               data-bs-target="#addSymptomModal">
                                <i class="fa fa-plus"></i> Add Symptom
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="symptomtbl" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Details</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="symptomsTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Symptom Modal -->
    <div class="modal fade" id="addSymptomModal" tabindex="-1" aria-labelledby="addSymptomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="symptomForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                        <h5 class="modal-title" id="addSymptomModalLabel">Add Symptom</h5>
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="symptomName" class="form-label">Symptom Name <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="symptomName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="symptomDetails" class="form-label">Details</label>
                            <textarea class="form-control" id="symptomDetails" name="details" rows="3" style="border-radius:10px"></textarea>
                        </div>
                        <div id="globalSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="globalError" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Symptom</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Symptom Modal -->
    <div class="modal fade" id="editSymptomModal" tabindex="-1" aria-labelledby="editSymptomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editSymptomForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editSymptomId" name="id">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #CFECE0; color:black">
                        <h5 class="modal-title" id="editSymptomModalLabel">Edit Symptom</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editSymptomName" class="form-label">Symptom Name <span
                                                        class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editSymptomName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSymptomDetails" class="form-label">Details</label>
                            <textarea class="form-control" id="editSymptomDetails" name="details" rows="3" style="border-radius:10px"></textarea>
                        </div>
                        <div id="editGlobalSuccess" class="alert alert-success" style="display:none;"></div>
                        <div id="editGlobalError" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Symptom</button>
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

<script>
$(document).ready(function() {
    let branchId = localStorage.getItem('selectedBranchId');
    if (!branchId) console.warn('Branch ID not found in localStorage!');

    const token = localStorage.getItem('token');

    // Initialize DataTable
    let symptomTable = $('#symptomtbl').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        "columnDefs": [{ "orderable": false, "targets": 2 }]
    });

    // Load Symptoms Table
    function loadSymptoms() {
        // Destroy the existing DataTable before reinitializing
        if ($.fn.DataTable.isDataTable('#symptomtbl')) {
            symptomTable.destroy();
        }

        $.ajax({
            url: '/api/symptoms',
            type: 'GET',
            data: { branch_id: branchId },
            headers: { "Authorization": "Bearer " + token },
            success: function(data) {
                symptomTable = $('#symptomtbl').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    "columnDefs": [{ "orderable": false, "targets": 2 }]
                });
                symptomTable.clear();
                data.forEach(function(symptom) {
                    symptomTable.row.add([
                        symptom.name || 'N/A',
                        symptom.details || 'N/A',
                        `<div class="icon" style="cursor:pointer">
                            @if (app('hasPermission')(7, 'update'))
                            <i class="fa fa-pencil m-r-5 icon1 edit-symptom" data-id="${symptom.id}" title="Edit"></i>
                            @endif
                            @if (app('hasPermission')(7, 'delete'))
                            <i class="fa fa-trash-o m-r-5 icon2 delete-symptom" data-id="${symptom.id}" title="Delete"></i>
                            @endif
                        </div>`
                    ]).draw(false);
                });
            },
            error: function(xhr) {
                console.error("Error loading symptoms:", xhr.responseText);
            }
        });
    }

    loadSymptoms();

    // Add Symptom
    $('#symptomForm').on('submit', function(e) {
        e.preventDefault();
        $('#globalSuccess,#globalError').hide();
        $.ajax({
            url: "{{ route('symptoms.store') }}",
            method: "POST",
            data: {
                name: $('#symptomName').val(),
                details: $('#symptomDetails').val(),
                branch_id: branchId
            },
            success: function(response) {
                $('#globalSuccess').text(response.message).fadeIn();
                setTimeout(() => {
                    $('#addSymptomModal').modal('hide');
                    $('#symptomForm')[0].reset();
                    $('#globalSuccess').fadeOut();
                    loadSymptoms();
                }, 1500);
            },
            error: function(xhr) {
                let errorText = xhr.responseJSON?.message || 'Something went wrong!';
                $('#globalError').text(errorText).fadeIn();
                setTimeout(() => $('#globalError').fadeOut(), 3000);
            }
        });
    });

    // Edit Symptom Modal
    $(document).on('click', '.edit-symptom', function() {
        const symptomId = $(this).data('id');
        $.ajax({
            url: '/api/symptoms/' + symptomId,
            type: 'GET',
            headers: { "Authorization": "Bearer " + token },
            success: function(symptom) {
                $('#editSymptomId').val(symptom.id);
                $('#editSymptomName').val(symptom.name);
                $('#editSymptomDetails').val(symptom.details);
                $('#editSymptomModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch symptom data.', 'error');
            }
        });
    });

    // Update Symptom
    $('#editSymptomForm').on('submit', function(e) {
        e.preventDefault();
        const symptomId = $('#editSymptomId').val();
        const formData = {
            name: $('#editSymptomName').val(),
            details: $('#editSymptomDetails').val(),
            branch_id: branchId
        };
        $.ajax({
            url: '/api/symptoms/' + symptomId,
            type: 'PUT',
            data: formData,
            headers: { "Authorization": "Bearer " + token },
            success: function(response) {
                $('#editGlobalSuccess').text(response.message).fadeIn();
                setTimeout(() => {
                    $('#editSymptomModal').modal('hide');
                    $('#editSymptomForm')[0].reset();
                    loadSymptoms();
                }, 1500);
            },
            error: function(xhr) {
                let errorText = xhr.responseJSON?.message || 'Something went wrong!';
                $('#editGlobalError').text(errorText).fadeIn();
                setTimeout(() => $('#editGlobalError').fadeOut(), 3000);
            }
        });
    });

    // Delete Symptom
    $(document).on('click', '.delete-symptom', function() {
        const symptomId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this symptom!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#cfece0',
            cancelButtonColor: '#f89884',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: { confirmButton: 'swal-confirm-btn', cancelButton: 'swal-cancel-btn' }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/symptoms/' + symptomId,
                    type: 'DELETE',
                    headers: { "Authorization": "Bearer " + token },
                    success: function(response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Symptom deleted successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => loadSymptoms());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete symptom.', 'error');
                    }
                });
            }
        });
    });

    // Initialize Select2 dropdown if needed
    $('#symptomDropdown').select2({
        placeholder: "Select Symptoms",
        width: '100%'
    }).ajax({
        url: "{{ route('symptoms.list') }}",
        data: { branch_id: branchId },
        dataType: 'json',
        processResults: function(data) {
            return { results: data.map(item => ({ id: item.id, text: item.name })) };
        }
    });
});
</script>

@endsection
