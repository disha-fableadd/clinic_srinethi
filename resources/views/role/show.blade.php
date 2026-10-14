@extends('layout.app')

@section('content')
<div class="page-wrapper">
    <div class="content" style="height:100vh">
        <div class="row">
            <div class="col-sm-4 col-3">
                <h4 class="page-title" style="text-align:left;">
                    <i class="fa fa-user-shield"></i> Role Details
                </h4>
            </div>
            @if(app('hasPermission')(2, 'view'))
                <div class="col-sm-8 col-9 text-right m-b-2">
                    <a href="{{ route('role.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left"></i> Back to Roles
                    </a>
                </div>
            @endif
        </div>

        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-footer text-right" style="background-color:#87ceb0">

                        <h3 style="float:left" class="text-dark"><i class="fa fa-info-circle icon-style2"></i> <span
                                class="role_name"></span> Details
                        </h3>


                    </div>

                    <div class="card-body mt-3">

                        <p class="text-dark"><strong><i class="fa fa-id-badge icon-style1"></i> Role Name : </strong>
                            <span class="role_name"></span>
                        </p>


                        <hr>
                        <p class="text-dark"><strong><i class="fa fa-align-left icon-style1"></i> Description :
                            </strong>
                            <span id="role_description"></span>
                        </p>
                        <hr>

                        <p class="text-dark"><strong><i class="fa fa-calendar-plus icon-style1"></i> Joining
                                Date : </strong>
                            <span id="role_created_at"></span>
                        </p>
                        <hr>
                        <p class="text-dark"><strong><i class="fa fa-calendar-check icon-style1"></i> Last
                                Updated :
                            </strong>
                            <span id="role_updated_at"></span>
                        </p>

                        <div class="button mb-4" style="display: flex; justify-content: end; margin: 0 5px;">
                            @if(app('hasPermission')(2, 'update'))
                                <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-role-btn"
                                    style="color:black; margin-right:10px">
                                    <i class="fa fa-pencil-alt"></i> Edit Role
                                </a>
                            @endif

                            @if(app('hasPermission')(2, 'delete'))
                                <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-role"
                                    data-id="{{ $role_id }}">
                                    <i class="fa fa-trash"></i> Delete Role
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
        let token = localStorage.getItem('token');

        if (!token) {
            // Redirect to login if no token is found
            window.location.href = "{{ route('login') }}";
        }
    });

    $(document).ready(function () {
        var roleId = "{{ $role_id }}";

        $.ajax({
            url: '/api/rolee/' + roleId,
            type: 'GET',
            dataType: 'json',
            headers: { "Authorization": "Bearer " + localStorage.getItem('token') },

            success: function (role) {

                $('.role_name').text(role.name);
                // console.log(role.name);

                $('#role_description').text(role.description);
                $('#role_created_at').text(role.created_at.split('T')[0]);
                $('#role_updated_at').text(role.updated_at.split('T')[0]);
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    // alert("Unauthorized: Please log in first.");
                    window.location.href = "{{ route('login') }}";
                }
            },

        });
    });



    $(document).on('click', '.delete-role', function () {
        var roleId = $(this).data('id');
        console.log("Deleting Role ID:", roleId);

        if (!roleId) {
            Swal.fire('Error', 'Role ID is missing!', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/rolee/' + roleId,
                    type: 'DELETE',
                    headers: { "Authorization": "Bearer " + localStorage.getItem('token') },
                    success: function (response) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Role deleted successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('button[data-id="' + roleId + '"]').closest('tr').remove();
                            setTimeout(function () {
                                window.location.href = "{{ route('role.index') }}";
                            }, 2000);
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 401) {
                            Swal.fire('Unauthorized', 'Your session has expired. Please login again.', 'warning').then(() => {
                                window.location.href = "{{ route('login') }}";
                            });
                        } else {
                            Swal.fire('Error', 'Failed to delete role.', 'error');
                        }
                    }
                });
            }
        });
    });



    $(document).ready(function () {
        var pathParts = window.location.pathname.split('/');
        var roleId = pathParts[pathParts.length - 1];

        console.log("Extracted Role ID from URL:", roleId);

        if (!roleId || isNaN(roleId)) {
            alert("Error: Role ID is missing or invalid.");
            return;
        }

        $("#roleId").val(roleId);


        $.ajax({
            url: "/api/rolee/" + roleId,
            type: "GET",
            headers: { "Authorization": "Bearer " + localStorage.getItem('token') },

            success: function (role) {
                $("#roleName").val(role.name);
                $("#roleDescription").val(role.description);
                $(".edit-role-btn").attr("href", "/role/edit/" + role.id);
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    // alert("Unauthorized: Please log in first.");
                    window.location.href = "{{ route('login') }}";
                }
            },

        });


    });
</script>













<style>
    /* .card-footer {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    } */

    .icon-style1 {
        background-color: white;
        color: rgb(157 195 179);
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }

    .icon-style2 {
        /* background-color: white; */
        color: white;
        padding: 5px;
        font-size: 20px;
        border-radius: 50%;
    }
</style>
@endsection