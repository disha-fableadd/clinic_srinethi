@extends('layout.app')



<style>
    button.btn.btn-link.expand-btn {
    background: #f89884;
    color: white;
    padding: 7px;
    border-radius: 9px;
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

 

/* Ensure all headers are vertically aligned */
#usertbl thead th {
    vertical-align: middle !important;
    padding: 8px !important;
}

/* Optional: Ensure the table is responsive and compact */
table.dataTable {
    width: 100% !important;
    table-layout: fixed !important;
}


    table.dataTable {
        width: 100% !important;
        table-layout: auto !important;
    }

    /* ✅ Hide mobile (expandable) layout on larger screens */
    @media (min-width: 768px) {
        .d-table-cell.d-md-none {
            display: none !important;
        }
        .details-row {
            display: none !important;
        }
    }

    /* ✅ Hide desktop table columns on small screens */
    @media (max-width: 767px) {
        .d-none.d-md-table-cell {
            display: none !important;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content" style="height:100vh">
            {{-- <div class="row mt-3">
                <div class="col-sm-12 col-12">
                    <h4 class="page-title" style="text-align:left; !important">All Staffs Details</h4>
                </div>

            </div> --}}

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block text-white"><i class="fas fa-user  px-2"
                                    style="font-size:20px"></i>All Staffs </h3>
                            <button class="btn btn-rounded btn-hdr" id="exportButton">
                                <i class="fa fa-download"></i> <span class="hdr-btn-text">Export</span>
                            </button>

                            @if (app('hasPermission')(3, 'create'))
                                <a href="{{ route('user.create') }}" class="btn btn-rounded btn-hdr">
                                    <i class="fa fa-plus"></i> <span class="hdr-btn-text">Add</span>
                                </a>
                            @endif
                        </div>
                        <div class="card-body ">
                            @php
                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                            @endphp
                            <div class="table-responsive">
                                <div id="demo_info" class="box"></div>
                                <table id="usertbl" class="table  custom-table">
                                    <thead style="background-color:rgb(254 217 207);">
                                        <tr>
                                            <th class="d-none">ID</th>
                                            <th>Staff</th>
                                            <th class="d-none d-md-table-cell">Email</th>
                                            <th class="d-none d-md-table-cell">Mobile</th>
                                            <th class="d-none d-md-table-cell" style="min-width: 110px;">Join Date</th>
                                            @if($currentProjectTypeId !== 3)
                                            <th class="d-none d-md-table-cell">Role</th>
                                            @endif
                                            <th class="d-none d-md-table-cell" >Action</th>
                                            <th class="d-table-cell d-md-none">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userbody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="changePasswordForm">
                <input type="hidden" id="userIdForPassword">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#cfece0">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <br><br>
                        <div id="successMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </div>
            </form>

        </div>
    </div>



    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>



    <script>
        $(document).on('click', '.change-password', function () {

            if (window.innerWidth <= 450) {
                document.querySelector('input[type="search"]').placeholder = "Search";
            }

            const userId = $(this).data('id');
            $('#userIdForPassword').val(userId);
            $('#changePasswordForm')[0].reset();
            $('#changePasswordModal').modal('show');
        });

       


        $('#changePasswordForm').submit(function (e) {
            e.preventDefault();

            // Clear previous messages
            $('#successMessage').hide().text('');
            $('#errorMessage').hide().text('');

            const password = $('input[name="password"]').val();
            const confirmPassword = $('input[name="password_confirmation"]').val();

            // Validation: Check if passwords match
            if (password !== confirmPassword) {
                $('#errorMessage').html('Confirm password does not match.').show();
                return; // Stop form submission
            }

            const formData = {
                user_id: $('#userIdForPassword').val(),
                password: password,
                password_confirmation: confirmPassword,
            };

            $.ajax({
                url: 'api/users/change-password',
                type: 'POST',
                data: formData,
                success: function (response) {
                    $('#successMessage').text(response.message).show();

                    setTimeout(() => {
                        $('#changePasswordModal').modal('hide');
                        $('#successMessage').hide();

                        if (response.logout) {
                            window.location.href = '/logout';
                        }
                    }, 2000);
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON.errors;
                    let message = 'An error occurred.';

                    if (errors) {
                        message = Object.values(errors).flat().join('<br>');
                    } else if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    $('#errorMessage').html(message).show();
                }
            });
        });



        
        $(document).on('click', '#exportButton', function () {
            let branchId = localStorage.getItem('selectedBranchId');
            let branchName = localStorage.getItem('selectedBranchName');

            // Check if branch info exists
            if (!branchId || !branchName) {
                alert('Please select a branch first!');
                return;
            }

            // Redirect to export route with query params
            window.location.href =
                `/staff/export?branch_id=${branchId}&branch_name=${encodeURIComponent(branchName)}`;
        });



        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle_btn');
            toggleBtn.addEventListener('click', function () {
                document.body.classList.toggle('mini-sidebar');
            });
        });
      






        $(document).ready(function () {
            let userTable;

            function ucfirst(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function buildActionsHtml(user) {
                let html = `<div class="icon" style="cursor:pointer">`;
                @if (app('hasPermission')(3, 'view'))
                    html += `<i class="fa fa-eye m-r-5 icon3 view-user" data-id="${user.id}" title="View"></i>`;
                @endif
                @if (app('hasPermission')(3, 'update'))
                    html += `<i class="fa fa-pencil m-r-5 icon1 edit-user" data-id="${user.id}" title="Edit"></i>`;
                @endif
                @if (app('hasPermission')(3, 'delete'))
                    html += `<i class="fa fa-trash-o m-r-5 icon2 delete-user" data-id="${user.id}" title="Delete"></i>`;
                @endif
                @if (Auth::check() && optional(Auth::user()->role)->name == 'Admin')
                    html += `<i class="fa fa-key m-r-5 icon4 change-password" data-id="${user.id}"></i>`;
                @endif
                html += `</div>`;
                return html;
            }

            function initializeUsersTable() {
                if ($.fn.DataTable.isDataTable("#usertbl")) {
                    try {
                        let existingTable = $('#usertbl').DataTable();
                        existingTable.destroy();
                        $('#usertbl').removeClass('dataTable');
                        $('#usertbl tbody').empty();
                        $.removeData($('#usertbl')[0], 'DataTable');
                        $.removeData($('#usertbl')[0], 'DataTables_DataTable');
                        $('#usertbl').off();
                    } catch (e) {
                        $('#usertbl tbody').empty();
                        $.removeData($('#usertbl')[0]);
                    }
                }

                if ($('#usertbl tbody').length === 0) {
                    $('#usertbl').append('<tbody id="userbody"></tbody>');
                }

                setTimeout(function () {
                    userTable = $('#usertbl').DataTable({
                        processing: true,
                        serverSide: true,
                        retrieve: true,
                        destroy: true,
                        ajax: function (data, callback) {
                            let branchId = localStorage.getItem('selectedBranchId');
                            branchId = branchId ? parseInt(branchId) : null;
                            const page = Math.floor(data.start / data.length) + 1;

                            $.ajax({
                                url: '/api/users',
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
                                success: function (res) {
                                    callback({
                                        draw: data.draw,
                                        recordsTotal: res.pagination?.total || res.recordsTotal || 0,
                                        recordsFiltered: res.pagination?.total || res.recordsFiltered || 0,
                                        data: res.data || []
                                    });
                                },
                                error: function () {
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
                            { data: 'id', visible: false, searchable: false },
                            {
                                data: 'fullname',
                                render: function (data, type, row) {
                                    const name = data ? ucfirst(data) : 'N/A';
                                    const profile = row.profile || '';
                                    return `
                                        <img src="${profile}" class="rounded-circle" width="50" height="50">
                                        <span><h2>${name}</h2></span>
                                    `;
                                },
                                className: "view-user",
                                createdCell: function (td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                orderable: false
                            },
                            {
                                data: 'email',
                                className: "d-none d-md-table-cell view-user",
                                createdCell: function (td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                render: data => data || 'N/A',
                                orderable: false
                            },
                            {
                                data: 'phone',
                                className: "d-none d-md-table-cell view-user",
                                createdCell: function (td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                render: data => data || 'N/A',
                                orderable: false
                            },
                            {
                                data: 'joinDate',
                                className: "d-none d-md-table-cell view-user",
                                createdCell: function (td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                render: data => data || 'N/A',
                                orderable: false
                            },
                            @if($currentProjectTypeId !== 3)
                            {
                                data: 'roleName',
                                className: "d-none d-md-table-cell view-user",
                                createdCell: function (td, cellData, rowData) {
                                    $(td).attr('data-id', rowData.id).css('cursor', 'pointer');
                                },
                                render: data => data ? ucfirst(data) : 'N/A',
                                orderable: false
                            },
                            @endif
                            {
                                data: 'id',
                                className: "d-none d-md-table-cell",
                                render: function (data, type, row) {
                                    return buildActionsHtml(row);
                                },
                                orderable: false
                            },
                            {
                                data: null,
                                render: function () {
                                    return `
                                        <button class="btn btn-link expand-btn">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    `;
                                },
                                className: "d-table-cell d-md-none",
                                orderable: false
                            }
                        ],
                        order: [[0, 'desc']]
                    });
                }, 50);
            }

            initializeUsersTable();

            $(document).on('click', '.expand-btn', function () {
                const btn = $(this);
                const icon = btn.find('i');
                const tr = btn.closest('tr');
                const existingRow = tr.next('.details-row');

                if (existingRow.length) {
                    existingRow.slideToggle(300);
                    icon.toggleClass('fa-chevron-down fa-chevron-up');
                    return;
                }

                let rowData = null;
                if (userTable) {
                    const row = userTable.row(tr);
                    rowData = row.data();
                }

                const email = rowData?.email || 'N/A';
                const phone = rowData?.phone || 'N/A';
                const joinDate = rowData?.joinDate || 'N/A';
                const role = rowData?.roleName ? ucfirst(rowData.roleName) : 'N/A';
                const actionHtml = rowData ? buildActionsHtml(rowData) : '';

                const detailsRow = $(`
                    <tr class="details-row">
                        <td colspan="8">
                            <div class="details-content">
                                <div><strong>Email:</strong> ${email}</div>
                                <div><strong>Mobile:</strong> ${phone}</div>
                                <div><strong>Join Date:</strong> ${joinDate}</div>
                                @if($currentProjectTypeId !== 3)
                                <div><strong>Role:</strong> ${role}</div>
                                @endif
                                <div class="mt-2"><strong>Action:</strong> ${actionHtml}</div>
                            </div>
                        </td>
                    </tr>
                `);

                tr.after(detailsRow);
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });



            $(document).on('click', '.view-user', function () {
                var userId = $(this).data('id');
                window.location.href = '/user/show/' + userId;
            });

            $(document).on('click', '.edit-user', function () {
                var userId = $(this).data('id');
                window.location.href = '/user/edit/' + userId;
            });
            $(document).on('click', '.delete-user', function () {
                var userId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete user!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#cfece0', // background for Yes
                    cancelButtonColor: '#f89884', // background for Cancel
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'swal-confirm-btn', // ✅ custom class
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/users/' + userId,
                            type: 'DELETE',
                            dataType: 'json',
                            headers: {
                                "Authorization": "Bearer " + token
                            },
                            success: function (data) {
                                // Remove the deleted row from the table
                                $('button[data-id="' + userId + '"]').closest('tr')
                                    .remove();

                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'User deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location
                                        .reload(); // Reload page to reflect changes
                                });
                            },
                            error: function (xhr, status, error) {
                                console.log(xhr.responseText);
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'An error occurred while deleting the user.',
                                    'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
