@extends('layout.app')

<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/category-create.css') }}">
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row row-padding-top">
                <div class="col-6">
                    <h4 class="page-title category-title  ">Add Category</h4>
                </div>

                <div class="col-6 category-button  m-b-2 ">
                    <a href="{{ route('category.index') }}" class="btn btn-primary  btn-rounded">
                        <i class="fa fa-arrow-left m-r-5 icon3  "></i> <span class="hdr-btn-text">Back</span></a>
                </div>

            </div>
            <div class="row">
                <div class="offset-lg-2">
                    <form id="categoryForm" class="form-container category-form" method="POST">
                        @csrf
                        <input type="hidden" name="branch_id" id="branch_id">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><i class="fas fa-user-tag  icon-style"></i> Category Name <span
                                                        class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label> <i class="fas fa-info-circle  icon-style"></i> Description <span
                                                        class="text-danger">*</span></label>
                                    <textarea cols="30" rows="4" class="form-control description-textarea" name="description"></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="categorysuccessMessage" class="alert alert-success" style="display:none;"></div>
                        <div id="categoryerrorMessage" class="alert alert-danger" style="display:none;"></div>
                        <div class="m-t-20 text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Create Category</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });
        $(document).ready(function() {


            // Initialize form validation
            $('#categoryForm').validate({
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
                        required: "Please enter the category name",

                    },
                    description: {
                        required: "Please enter a description",

                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                if ($('#categoryForm').valid()) {
                    let formData = new FormData(this);

                    $.ajax({
                        url: "{{ url('api/category') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer " + token
                        },

                        success: function(response) {
                            $('#categorysuccessMessage').text(response.message ||
                                'Category created successfully').show();
                            $('#categoryForm')[0].reset();
                            setTimeout(function() {
                                window.location.href = "{{ route('category.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422) { // Validation error
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            }
                            $('#categoryerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
