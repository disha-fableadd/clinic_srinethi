@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/category-edit.css') }}">
@section('content')

    <div class="page-wrapper">
        <div class="content">
            <div class="row row-padding-top">
                <div class=" col-6">
                    <h4 class="page-title category-title " >Edit Category</h4>
                </div>


                <div class="col-6 category-button m-b-2 ">
                    <a href="{{ route('category.index') }}" class="btn btn-primary  btn-rounded">
                        <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span></a>
                </div>

            </div>
            <div class="row">
                <div class="offset-lg-2">
                    <form id="editCategoryForm" class="form-container category-form">
                        @csrf
                        <input type="hidden" id="categoryId">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-tag icon-style"></i> Category Name <span
                                                        class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="categoryName" name="name" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><i class="fas fa-info-circle icon-style"></i> Description <span
                                                        class="text-danger">*</span></label>
                                    <textarea cols="30" rows="4" class="form-control description-textarea" id="categoryDescription"
                                        name="description"></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="editcatsuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="editcaterrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Update Category</button>
                        </div>
                    </form>



                </div>
            </div>
        </div>

    </div>

    <!-- Include jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle_btn');
            toggleBtn.addEventListener('click', function () {
                document.body.classList.toggle('mini-sidebar');
            });
        });

        $(document).ready(function () {
         




            // Initialize form validation
            $('#editCategoryForm').validate({
                rules: {
                    name: {
                        required: true,

                    },
                    description: {
                        required: true,

                    }
                },
                messages: {
                    name: {
                        required: "Please enter the Category name",

                    },
                    description: {
                        required: "Please enter a description",

                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            var pathParts = window.location.pathname.split('/');
            var categoryId = pathParts[pathParts.length - 1]; // Get last part



            if (!categoryId || isNaN(categoryId)) {
                alert("Error: Category ID is missing or invalid.");
                return;
            }

            $("#categoryId").val(categoryId); // Set hidden input field

            // Fetch role data using API
            $.ajax({
                url: "/api/category/" + categoryId,
                type: "GET",
                headers: { "Authorization": "Bearer " + token },
                success: function (response) {
                   // console.log("API Response:", response);
                    if (response.category) {
                        $("#categoryName").val(response.category.name);
                        $("#categoryDescription").val(response.category.description);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        window.location.href = "{{ route('login') }}";
                    }
                },
            });

            $("#editCategoryForm").submit(function (event) {
                event.preventDefault();

                if ($('#editCategoryForm').valid()) {
                    let categoryId = $("#categoryId").val(); // Fetch from input
                    let categoryName = $("#categoryName").val();
                    let categoryDescription = $("#categoryDescription").val();



                    if (!categoryId) {
                        alert("Error: category ID is missing before updating.");
                        return;
                    }

                    $.ajax({
                        url: "/api/category/" + categoryId,
                        type: "PUT",
                        data: JSON.stringify({
                            name: categoryName,
                            description: categoryDescription
                        }),
                        contentType: "application/json",
                        // headers: { "Authorization": "Bearer " + token },

                        success: function (response) {
                            //console.log("Update Success:", response);
                            $("#editcatsuccessMessage").text("category updated successfully!").fadeIn().delay(3000).fadeOut();

                            setTimeout(function () {
                                window.location.href = "{{ route('category.index') }}";
                            }, 2000);
                        },
                        error: function (xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) { // Validation error
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#editcaterrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>

@endsection