@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/category-show.css') }}">
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row">
                <div class="col-sm-6 col-8">
                    <h4 class="page-title">
                        <i class="fa fa-user-shield"></i> Category Details
                    </h4>
                </div>

                <div class="col-sm-6 col-4 text-right m-b-2">
                    <a href="{{ route('category.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                        <i class="fa fa-arrow-left"></i> <span class="hdr-btn-text">Back</span> 
                    </a>
                </div>

            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-footer text-right">

                            <h3 class="text-dark"><i class="fa fa-info-circle icon-style2"></i> <span
                                    class="category_name"></span> Details
                            </h3>


                        </div>

                        <div class="card-body mt-3">

                            <p class="text-dark"><strong><i class="fa fa-id-badge icon-style1"></i> Category Name :
                                </strong>
                                <span class="category_name"></span>
                            </p>


                            <hr>
                            <p class="text-dark"><strong><i class="fa fa-align-left icon-style1"></i> Description :
                                </strong>
                                <span id="category_description"></span>
                            </p>
                            <hr>

                            <!-- <p class="text-dark"><strong><i class="fa fa-calendar-plus icon-style1"></i> Joining
                                        Date : </strong>
                                    <span id="category_created_at"></span>
                                </p>
                                <hr> -->
                            <p class="text-dark"><strong><i class="fa fa-calendar-check icon-style1"></i> Last
                                    Updated :
                                </strong>
                                <span id="category_updated_at"></span>
                            </p>
                            <hr>

                            <div class="button mb-4">

                                <a href="#" class="btn btn-primary btn-rounded btn-hdr edit-category-btn">
                                    <i class="fa fa-pencil-alt"></i> <span class="hdr-btn-text">Edit</span> 
                                </a>



                                <button type="button" class="btn btn-danger btn-rounded btn-hdr delete-category"
                                    data-id="{{ $category_id }}">
                                    <i class="fa fa-trash"></i> <span class="hdr-btn-text">Delete</span> 
                                </button>


                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="successMessage" class="alert alert-success"></div>


            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>


    <script>






        $(document).ready(function () {
            var categoryId = "{{ $category_id }}";

            $.ajax({
                url: '/api/category/' + categoryId,
                type: 'GET',
                dataType: 'json',
                headers: { "Authorization": "Bearer " + token },

                success: function (response) {
                    //console.log("API Response:", response); // Debugging

                    if (response.category) {
                        let category = response.category;

                        function ucfirst(str) {
                            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
                        }

                        $('.category_name').text(ucfirst(category.name));
                        $('#category_description').text(ucfirst(category.description));

                        $('#category_created_at').text(category.created_at ? category.created_at.split('T')[0] : 'N/A');
                        $('#category_updated_at').text(category.updated_at ? category.updated_at.split('T')[0] : 'N/A');
                    }

                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText); // Debugging
                    if (xhr.status === 401) {
                        window.location.href = "{{ route('login') }}";
                    }
                },
            });

        });



        $(document).on('click', '.delete-category', function () {
            var categoryId = $(this).data('id');
            console.log("Deleting category ID:", categoryId);

            if (!categoryId) {
                Swal.fire('Error', 'category ID is missing!', 'error');
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
                        url: '/api/categorye/' + categoryId,
                        type: 'DELETE',
                        headers: { "Authorization": "Bearer " + token },
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'category deleted successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('button[data-id="' + categoryId + '"]').closest('tr').remove();
                                setTimeout(function () {
                                    window.location.href = "{{ route('category.index') }}";
                                }, 2000);
                            });
                        },
                        error: function (xhr) {
                            if (xhr.status === 401) {
                                Swal.fire('Unauthorized', 'Your session has expired. Please login again.', 'warning').then(() => {
                                    window.location.href = "{{ route('login') }}";
                                });
                            } else {
                                Swal.fire('Error', 'Failed to delete category.', 'error');
                            }
                        }
                    });
                }
            });
        });



        $(document).ready(function () {
            var pathParts = window.location.pathname.split('/');
            var categoryId = pathParts[pathParts.length - 1];

            console.log("Extracted category ID from URL:", categoryId);

            if (!categoryId || isNaN(categoryId)) {
                alert("Error: category ID is missing or invalid.");
                return;
            }

            $("#categoryId").val(categoryId);


            $.ajax({
                url: "/api/category/" + categoryId,
                type: "GET",
                // headers: { "Authorization": "Bearer " + localStorage.getItem('token') },

                success: function (category) {
                    $("#categoryName").val(category.name);
                    $("#categoryDescription").val(category.description);
                    $(".edit-category-btn").attr("href", "/category/edit/" + categoryId);
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



@endsection