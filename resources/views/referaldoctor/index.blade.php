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
      colgroup { display: none; }

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

    #doctorTbl thead th {
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
        .d-table-cell.d-md-none { display: none !important; }
        .details-row { display: none !important; }
    }

    @media (max-width: 767px) {
        .d-none.d-md-table-cell { display: none !important; }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" >
                            <h3 class="card-title d-inline-block text-white">
                                <i class="fa fa-user-md px-2" style="font-size:20px"></i> All Referral Doctors
                            </h3>
                            @if (app('hasPermission')(39, 'create'))
                                <a href="javascript:void(0)" class="btn btn-rounded btn-hdr" data-bs-toggle="modal"
                                    data-bs-target="#addDoctorModal">
                                    <i class="fa fa-plus"></i> Add Doctor
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="doctorTbl" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th >Specialist</th>
                                            <th class="d-none d-md-table-cell">Email</th>
                                            <th class="d-none d-md-table-cell">Phone</th>
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

        <!-- Add Doctor Modal -->
        <div class="modal fade" id="addDoctorModal" tabindex="-1" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="doctorForm">
                    @csrf
                    <input type="hidden" name="branch_id" id="branch_id">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="addDoctorModalLabel">Add Referral Doctor</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="doctorName" class="form-label">Doctor Name</label>
                                <input type="text" class="form-control" id="doctorName" name="doctor_name">
                            </div>
                            <div class="mb-3">
                                <label for="specialist" class="form-label">Specialist</label>
                                <input type="text" class="form-control" id="specialist" name="specialist">
                            </div>
                            <div class="mb-3">
                                <label for="doctorEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="doctorEmail" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="doctorPhone" class="form-label">Phone Number</label>
                                <input type="number" class="form-control" id="doctorPhone" name="phone_number">
                            </div>
                            <div id="globalSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="globalError" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Doctor</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Doctor Modal -->
        <div class="modal fade" id="editDoctorModal" tabindex="-1" aria-labelledby="editDoctorModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="editDoctorForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editDoctorId" name="id">
                    <input type="hidden" id="editBranchId" name="branch_id">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #CFECE0; color:black">
                            <h5 class="modal-title" id="editDoctorModalLabel">Edit Referral Doctor</h5>
                            <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editDoctorName" class="form-label">Doctor Name</label>
                                <input type="text" class="form-control" id="editDoctorName" name="doctor_name">
                            </div>
                            <div class="mb-3">
                                <label for="editSpecialist" class="form-label">Specialist</label>
                                <input type="text" class="form-control" id="editSpecialist" name="specialist">
                            </div>
                            <div class="mb-3">
                                <label for="editDoctorEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="editDoctorEmail" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="editDoctorPhone" class="form-label">Phone Number</label>
                                <input type="number" class="form-control" id="editDoctorPhone" name="phone_number">
                            </div>
                            <div id="editGlobalSuccess" class="alert alert-success" style="display:none;"></div>
                            <div id="editGlobalError" class="alert alert-danger" style="display:none;"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Doctor</button>
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
        let doctorTable;

        document.addEventListener('DOMContentLoaded', function () {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
                document.getElementById('editBranchId').value = storedBranchId;
            }
        });

        $(document).ready(function () {
            const token = localStorage.getItem('token');
            const branchId = localStorage.getItem('selectedBranchId');

            function initializeDataTable() {
                if ($.fn.DataTable.isDataTable("#doctorTbl")) {
                    try {
                        let existingTable = $('#doctorTbl').DataTable();
                        existingTable.destroy();
                        $('#doctorTbl').removeClass('dataTable');
                        $('#doctorTbl tbody').empty();
                        $.removeData($('#doctorTbl')[0], 'DataTable');
                        $.removeData($('#doctorTbl')[0], 'DataTables_DataTable');
                        $('#doctorTbl').off();
                    } catch (e) {
                        $('#doctorTbl tbody').empty();
                        $.removeData($('#doctorTbl')[0]);
                    }
                }

                if ($('#doctorTbl tbody').length === 0) {
                    $('#doctorTbl').append('<tbody></tbody>');
                }

                setTimeout(function () {
                    doctorTable = $('#doctorTbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        responsive: false,
                        columnDefs: [
                            { orderable: false, targets: [4, 5] }
                        ],
                        ajax: function (data, callback) {
                            const page = Math.floor(data.start / data.length) + 1;
                            $.ajax({
                                url: '/api/referal_doctors',
                                type: 'GET',
                                headers: { "Authorization": "Bearer " + token },
                                data: {
                                    branch_id: branchId,
                                    page: page,
                                    per_page: data.length,
                                    search: data.search?.value || ''
                                },
                                success: function (res) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: res.pagination?.total || 0,
                                        recordsFiltered: res.pagination?.total || 0,
                                        data: res.data || []
                                    });
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error loading doctors:', error);
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
                            { data: "doctor_name", defaultContent: "N/A" },
                            { data: "specialist", defaultContent: "N/A" },
                            { data: "email", defaultContent: "N/A", className: "d-none d-md-table-cell" },
                            { data: "phone_number", defaultContent: "N/A", className: "d-none d-md-table-cell" },
                            {
                                data: "id",
                                render: function (data, type, row) {
                                    return `
                                        <div class="icon" style="cursor:pointer">
                                            @if (app('hasPermission')(10, 'update'))
                                                <i class="fa fa-pencil m-r-5 icon1 edit-doctor" data-id="${row.id}" title="Edit"></i>
                                            @endif
                                            @if (app('hasPermission')(10, 'delete'))
                                                <i class="fa fa-trash-o m-r-5 icon2 delete-doctor" data-id="${row.id}" title="Delete"></i>
                                            @endif
                                        </div>
                                    `;
                                },
                                className: "d-none d-md-table-cell"
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="btn btn-link expand-btn d-table-cell d-md-none">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                orderable: false,
                                className: "text-center d-table-cell d-md-none"
                            }
                        ],
                        order: [[0, 'asc']]
                    });
                }, 50);
            }

            initializeDataTable();

            // Add Doctor
            $('#doctorForm').on('submit', function (e) {
                e.preventDefault();
                $('#globalSuccess,#globalError').hide();
                $.ajax({
                    url: '/api/referal_doctors',
                    method: "POST",
                    headers: { "Authorization": "Bearer " + token },
                    data: {
                        doctor_name: $('#doctorName').val(),
                        specialist: $('#specialist').val(),
                        email: $('#doctorEmail').val(),
                        phone_number: $('#doctorPhone').val(),
                        branch_id: $('#branch_id').val()
                    },
                    success: function (response) {
                        $('#globalSuccess').text(response.message).fadeIn();
                        setTimeout(() => {
                            $('#addDoctorModal').modal('hide');
                            $('#doctorForm')[0].reset();
                            // loadDoctors();
                            location.reload();
                        }, 1000);
                    },
                    error: function (xhr) {
                        $('#globalError').text(xhr.responseJSON?.message || 'Something went wrong').fadeIn();
                    }
                });
            });

            // Edit Doctor
            $(document).on('click', '.edit-doctor', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/api/referal_doctors/${id}`,
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + token },
                    success: function (res) {
                        console.log(res); // 👈 Check what your API returns


                        const doctor = res;
                        if (!doctor) {
                            console.error("Doctor data missing:", res);
                            alert("Doctor data not found. Check API response.");
                            return;
                        }
                        $('#editDoctorId').val(doctor.id);
                        $('#editDoctorName').val(doctor.doctor_name);
                        $('#editSpecialist').val(doctor.specialization);
                        $('#editDoctorEmail').val(doctor.email);
                        $('#editDoctorPhone').val(doctor.phone);
                        $('#editDoctorModal').modal('show');
                    }
                });
            });

            // Update Doctor
            $('#editDoctorForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editDoctorId').val();
                $('#editGlobalSuccess,#editGlobalError').hide();
                $.ajax({
                    url: `/api/referal_doctors/${id}`,
                    type: 'PUT',
                    headers: { "Authorization": "Bearer " + token },
                    data: {
                        doctor_name: $('#editDoctorName').val(),
                        specialist: $('#editSpecialist').val(),
                        email: $('#editDoctorEmail').val(),
                        phone_number: $('#editDoctorPhone').val(),
                        branch_id: $('#editBranchId').val()
                    },
                    success: function (res) {
                        $('#editGlobalSuccess').text(res.message).fadeIn();
                        setTimeout(() => {
                            $('#editDoctorModal').modal('hide');
                            // loadDoctors();
                            location.reload();
                        }, 1000);
                    },
                    error: function (xhr) {
                        $('#editGlobalError').text(xhr.responseJSON?.message || 'Something went wrong').fadeIn();
                    }
                });
            });

            // Delete Doctor
            $(document).on('click', '.delete-doctor', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this doctor!",
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
                            url: `/api/referal_doctors/${id}`,
                            type: 'DELETE',
                            headers: { "Authorization": "Bearer " + token },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Referal doctor deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function (xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete doctor', 'error');
                            }
                        });
                    }
                });
            });
        });

         // Mobile Expand
    $(document).on('click', '.expand-btn', function () {
        const btn = $(this);
        const icon = btn.find('i');
        const tr = btn.closest('tr');
        const existing = tr.next('.details-row');

        $('.details-row').not(existing).remove();
        $('.expand-btn i').removeClass('fa-chevron-up').addClass('fa-chevron-down');

        if (existing.length) return existing.remove();

        let rowData = null;
        if (doctorTable) {
            const row = doctorTable.row(tr);
            rowData = row.data();
        }

        const email = rowData?.email || 'N/A';
        const phone = rowData?.phone_number || 'N/A';
        const actionHtml = tr.find('td').eq(4).html() || '';

        const detailRow = $(`
            <tr class="details-row">
                <td colspan="6">
                    <div class="details-content">
                        <div><strong>Email:</strong> ${email}</div>
                        <div><strong>Phone:</strong> ${phone}</div>
                        <div class="mt-2"><strong>Action:</strong> ${actionHtml}</div>
                    </div>
                </td>
            </tr>
        `);

        tr.after(detailRow);
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    </script>
@endsection
