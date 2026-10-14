@extends('layout.app')
<style>
    .container {
        width: 60%;
        margin: auto;
    }

    .header1 {
        background: #cfece0;
        color: black;
        padding: 10px 15px;
        font-size: 18px;
        font-weight: bold;
        margin-top: 50px;
        border-radius: 15px;
    }

    .card-body {

        height: auto;
        padding: 20px !important;
    }

    .card {
        margin: 0;
    }

    table {

        margin-top: 10px;
        border-radius: 10px;
        border: 2px solid #cfece0;
        border-collapse: collapse;
        width: 100%;

    }

    table thead tr th {
        background-color: #cfece0 !important;
    }


    th,
    td {
        padding: 10px;
        text-align: center;
    }

    thead,
    tr {
        border-bottom: 2px solid #cfece0;
    }

    th {
        background: #f4f4f4;
    }

    .select-all {
        margin: 10px 5px;
    }

    .card-footer{
        background-color:#87ceb0 !important;
    }



    @media (max-width: 767px) {
        /* #userProfile {
            width: 120px !important;
            height: 130px !important;
        }


        .margin-text {
            margin: 5px !important;
        } */

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-info-container {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
        }

        .user-info-text {
            margin-left: 10px;
            width: 100%;
        }

        /* #screen-big {
            display: none ;
        } */
        #screen-small {
            display: block;
        }

        /* .icon-style1 {
         
            
            font-size: 15px;
            
        } */
      /* .margin-img{
            margin: auto 0;
        } */

        .card-body {
            padding: 10px 12px !important;
        }

        .card {
            margin: 5px !important;
        }

        .row.mt-3 {
            margin-top: 5px !important;
        }
        
        .page-wrapper > .content,
        .content {
            padding: 10px !important;
        }
    }







    .action-buttons,
    .content .button,
    .button {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-top: 20px;
        margin-bottom: 24px;
    }

    @media screen and (max-width: 767px) {
        .page-wrapper > .content,
        .content {
            height: auto !important;
            min-height: calc(100vh - 60px) !important;
            padding-bottom: calc(88px + env(safe-area-inset-bottom)) !important;
        }

        .action-buttons,
        .content .button,
        .button {
            margin-top: 20px !important;
            margin-bottom: calc(25px + env(safe-area-inset-bottom)) !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 12px !important;
            width: 100% !important;
        }

        .action-buttons .btn,
        .content .button .btn,
        .button .btn {
            margin: 0 !important;
            min-width: 110px !important;
            height: 38px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            border-radius: 50px !important;
        }

        .permission-scroll-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #ccc;
            /* Optional: visual scroll area */
        }

        .permission-table {
            min-width: 600px;
            /* or however wide your full table needs */
            width: max-content;
        }
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row mt-3">
                <div class="col-sm-8 col-8">
                    <h4 class="page-title" style="text-align:left;">
                        Staff's Information
                    </h4>
                </div>
                @if(app('hasPermission')(3, 'view'))
                    <div class="col-sm-4 col-4 text-right m-b-2">
                        <a href="{{ route('user.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">
                            <h3 style="float:left" class="text-dark">
                                <i class="fa fa-info-circle icon-style2"></i>
                                <span class=""> <span class="userFullName"></span> Details</span>
                            </h3>
                        </div>

                        <div class="card-body">


                        

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-lg-6 col-4 text-center margin-img">
                                            <p class="text-dark">
                                                <!-- <strong><i class="fa fa-image icon-style1 "></i> Image: </strong><br> -->
                                                <img id="userProfile" class="userProfile" src="" width="200" height="200"
                                                    style="border-radius:20px">
                                            </p>
                                        </div>
                                        <div class="col-lg-6 col-8">
                                            @php
                                                $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);
                                            @endphp
                                            @if($currentProjectTypeId !== 3)
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-user-tag icon-style1"></i> User Role: </strong>
                                                <span id="userRole"></span>
                                            </p>
                                            <hr class="margin-text">
                                            @endif
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-user-circle icon-style1"></i> Name: </strong>
                                                <span class="userFullName"></span>
                                            </p>
                                            <hr class="margin-text">

                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-phone icon-style1"></i> Phone: </strong>
                                                <span id="userPhone"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-genderless icon-style1"></i> Gender: </strong>
                                                <span id="userGender"></span>
                                            </p>
                                            <hr class="margin-text">
                                            <p class="text-dark margin-text">
                                                <strong><i class="fa fa-envelope icon-style1"></i> Email: </strong>
                                                <span id="userEmail"></span>
                                            </p>
                                            <hr class="margin-text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-home icon-style1"></i> Address:</strong>
                                        <span id="userAddress"></span>
                                    </p>

                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-city icon-style1"></i> City: </strong>
                                        <span id="userCity"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-map-marker-alt icon-style1"></i> State: </strong>
                                        <span id="userState"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-clock icon-style1"></i> Shift:</strong>
                                        <span id="userShift"></span>
                                    </p>
                                    <hr class="margin-text">
                                    <p class="text-dark margin-text">
                                        <strong><i class="fa fa-calendar-alt icon-style1"></i> Birth Date: </strong>
                                        <span id="userBirthDate"></span>
                                    </p>
                                    <hr class="margin-text">
                                </div>
                            </div>






                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card" style="border: 1px solid #87ceb0;">
                                        <div class="card-footer text-right" style="background-color:#87ceb0">
                                            <h3 style="float:left" class="text-dark"><i
                                                    class="fa fa-info-circle icon-style2"></i> Permissions</h3>

                                        </div>

                                        <div class="card-body">
                                            <div class="permission-scroll-wrapper">
                                                <table class="pb-5 permission-table">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th>Module Name</th>
                                                            <th>View</th>
                                                            <th>Insert</th>
                                                            <th>Edit</th>
                                                            <th>Delete</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody id="modules">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="button mb-4 action-buttons">
                                @if(app('hasPermission')(3, 'update'))
                                    <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-user-btn"
                                        style="color:black;">
                                        <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span> 
                                    </a>
                                @endif
                                @if(app('hasPermission')(3, 'delete'))
                                    <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-user"
                                        data-id="{{ $user_id }}">
                                        <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span> 
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success" style="display:none;"></div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        $(document).ready(function () {


            var userId = "{{ $user_id }}";

            // Fetch modules and create checkboxes
            fetch('/api/modules')
                .then(response => response.json())
                .then(modules => {
                    const tbody = $('tbody');
                    modules.forEach(module => {
                        const row = $('<tr>').attr('data-module-id', module.id);
                        row.append(`<td>${module.name}</td>`);

                        const permissions = ['view', 'create', 'update', 'delete'];

                        // Create checkboxes and disable by default
                        permissions.forEach(permission => {
                            const checkbox = $('<input>', {
                                type: 'checkbox',
                                class: permission
                            }).prop('disabled', true); // Disable by default

                            const td = $('<td>').append(checkbox);
                            row.append(td);
                        });
                        tbody.append(row);
                    });
                });

            // Fetch user details and permissions
            $.ajax({
                url: '/api/users/' + userId,
                type: 'GET',
                dataType: 'json',
                headers: { "Authorization": "Bearer " + token },
                success: function (data) {
                    //console.log(data);

                    // Set user details
                    // $('.userFullName').text(data.fullname);
                    // $('#userRole').text(data.roleName);
                    // // $('#userUsername').text(data.username);
                    // $('#userEmail').text(data.email);
                    // $('#userPhone').text(data.phone);
                    // if (data.profile) {
                    //     $('#userProfile').attr('src', data.profile);
                    // } else {
                    //     $('#userProfile').attr('src', '/admin/assets/img/img1.png');
                    // }
                    // $('#userAddress').text(data.address ?? 'N/A');
                    // $('#userCity').text(data.city ?? 'N/A');
                    // $('#userState').text(data.state ?? 'N/A');
                    // $('#userGender').text(data.gender ?? 'N/A');
                    // $('#userBirthDate').text(data.birth_date ?? 'N/A');
                    // $('#userShift').text(data.shift ?? 'N/A');
                    // $('#userSalary').text(data.salary ?? 'N/A');

                    $('.userFullName').text(data.fullname ? data.fullname.charAt(0).toUpperCase() + data.fullname.slice(1) : 'N/A');
                    $('#userRole').text(data.roleName ? data.roleName.charAt(0).toUpperCase() + data.roleName.slice(1) : 'N/A');
                    $('#userEmail').text(data.email || 'N/A');
                    $('#userPhone').text(data.phone || 'N/A');
                    if (data.profile) {
                        $('#userProfile').attr('src', data.profile);
                    } else {
                        $('#userProfile').attr('src', '/admin/assets/img/img1.png');
                    }
                    $('#userAddress').text(data.address ? data.address.charAt(0).toUpperCase() + data.address.slice(1) : 'N/A');
                    $('#userCity').text(data.city ? data.city.charAt(0).toUpperCase() + data.city.slice(1) : 'N/A');
                    $('#userState').text(data.state ? data.state.charAt(0).toUpperCase() + data.state.slice(1) : 'N/A');
                    $('#userGender').text(data.gender ? data.gender.charAt(0).toUpperCase() + data.gender.slice(1) : 'N/A');
                    $('#userBirthDate').text(data.birth_date || 'N/A');
                    $('#userShift').text(data.shift || 'N/A');
                    $('#userSalary').text(data.salary || 'N/A');


                    // Enable checkboxes based on permissions
                    data.permissions.forEach(permission => {
                        var moduleRow = $('tr[data-module-id="' + permission.module_id + '"]');

                        ['view', 'create', 'update', 'delete'].forEach(function (permissionType) {
                            var checkbox = moduleRow.find('.' + permissionType);
                            if (checkbox.length) {
                                if (permission[permissionType] == 1) {
                                    checkbox.prop('checked', true);
                                    checkbox.prop('disabled', true); // Checked and disabled
                                } else {
                                    checkbox.prop('checked', false);
                                    checkbox.prop('disabled', true); // Unchecked and disabled
                                }
                            }
                        });
                    });
                },
                error: function () {
                    alert('Failed to fetch user details.');
                }
            });

            // Populate form fields if userId is present
            if (userId) {
                $.ajax({
                    url: '/api/users/' + userId,
                    method: 'GET',
                    headers: { "Authorization": "Bearer " + token },
                    success: function (data) {
                        data.permissions.forEach(permission => {
                            var moduleRow = $('tr[data-module-id="' + permission.module_id + '"]');

                            ['view', 'create', 'update', 'delete'].forEach(function (permissionType) {
                                var checkbox = moduleRow.find('.' + permissionType);
                                if (checkbox.length) {
                                    if (permission[permissionType] == 1) {
                                        checkbox.prop('checked', true);
                                        checkbox.prop('disabled', true); // Enable only if the user has permission
                                    }
                                }
                            });
                        });

                        // Populate the form fields with the user data
                        $('input[name="username"]').val(data.username);
                        $('input[name="fullname"]').val(data.fullname);
                        $('input[name="email"]').val(data.email);
                        $('input[name="phone"]').val(data.phone);
                        $('input[name="gender"][value="' + data.gender + '"]').prop('checked', true);
                        $('input[name="birth_date"]').val(data.birth_date);
                        $('input[name="address"]').val(data.address);
                        $('select[name="city"]').val(data.city);
                        $('select[name="state"]').val(data.state);
                        $('select[name="shift"]').val(data.shift);
                        $('input[name="salary"]').val(data.salary);
                        $('#role-select').val(data.role_id); // Set selected role
                        $(".edit-user-btn").attr("href", "/user/edit/" + userId);
                    },
                    error: function (error) {
                        console.log('Error fetching user data:', error);
                    }
                });
            }

            // Delete user
            $(document).on('click', '.delete-user', function () {
                var userId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete user!",
                    icon: 'warning',
                    showCancelButton: true,


                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/users/' + userId,
                            type: 'DELETE',
                            dataType: 'json',
                            headers: { "Authorization": "Bearer " + sessionStorage.getItem('token') },
                            success: function (data) {
                                // Remove the deleted row from the table
                                $('button[data-id="' + userId + '"]').closest('tr').remove();

                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'User deleted successfully!',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Reload page to reflect changes
                                });
                            },
                            error: function (xhr, status, error) {
                                console.log(xhr.responseText);
                                Swal.fire('Error', xhr.responseJSON?.message || 'An error occurred while deleting the user.', 'error');
                            }
                        });
                    }
                });
            });




        });
    </script>


    <style>
        .icon-style1 {
            background-color: white;
            color: rgb(157, 195, 179);
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }

        .icon-style2 {
            color: white;
            padding: 5px;
            font-size: 20px;
            border-radius: 50%;
        }
    </style>
@endsection