@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/dietchart-create.css') }}"> 

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row mx-auto align-items-center" style="max-width: 700px; padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title m-0">Add Diet Chart</h4>
                </div>
                <div class="col-sm-6 col-6 text-right m-b-2">
                    <a href="{{ route('dietchart.index') }}" class="btn btn-primary btn-rounded btn-hdr" style="float: right;">
                        <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                    </a>
                </div>
            </div>

            <div class="diet-entry rounded p-3 mb-4 position-relative">
                <div class="row">
                    <div class="col-12">
                        <form id="createDietForm" class="form-container mx-auto" style="max-width: 700px; padding-bottom: 60px;"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="branch_id" id="branch_id">

                            <div class="container mt-4">
                                {{-- Template Name --}}
                                <div class="form-group">
                                    <label><i class="fas fa-utensils"></i> Template Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Enter template name">
                                </div>

                                {{-- Dynamic Fields --}}
                                <div id="dynamicFormContainer">
                                    <div class="form-row row mb-3 template-row align-items-end">
                                        <div class="col-md-6 col-12 mb-2">
                                            <label><i class="fas fa-heading"></i> Title <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title[]" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 col-12 mb-2">
                                            <label><i class="fas fa-align-left"></i> Description <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="description[]" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 col-12 mb-2">
                                            <label><i class="fas fa-clock"></i> Time <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="time[]" class="form-control"
                                                placeholder="e.g. Morning, Lunch, Dinner" required>
                                        </div>

                                        <div class="col-md-5 col-12 mb-2">
                                            <label><i class="fas fa-image"></i> Image</label>
                                            <input type="file" name="image[]" class="form-control" accept="image/*">
                                        </div>

                                        <div class="col-md-1 col-12 mb-2 text-md-left text-center">
                                            <button type="button" class="btn btn-primary btn-sm addRow">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Messages --}}
                                <div id="dietSuccess" class="alert alert-success" style="display:none;"></div>
                                <div id="dietError" class="alert alert-danger" style="display:none;"></div>

                                {{-- Submit --}}
                                <div class="m-t-20 text-center">
                                    <button type="submit" class="btn btn-primary submit-btn">Save Diet Chart</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });

        // Add / Remove rows
        $(document).ready(function() {
            $(document).on('click', '.addRow', function() {
                let row = `
        <div class="form-row row mb-3 template-row align-items-end">
            <div class="col-md-6 col-12 mb-2">
                <label><i class="fas fa-heading"></i> Title <span class="text-danger">*</span></label>
                <input type="text" name="title[]" class="form-control" required>
            </div>
            <div class="col-md-6 col-12 mb-2">
                <label><i class="fas fa-align-left"></i> Description <span class="text-danger">*</span></label>
                <input type="text" name="description[]" class="form-control" required>
            </div>
            <div class="col-md-6 col-12 mb-2">
                <label><i class="fas fa-clock"></i> Time <span class="text-danger">*</span></label>
                <input type="text" name="time[]" class="form-control" placeholder="e.g. Morning, Lunch, Dinner" required>
            </div>
            <div class="col-md-5 col-12 mb-2">
                <label><i class="fas fa-image"></i> Image </label>
                <input type="file" name="image[]" class="form-control" accept="image/*" >
            </div>
            <div class="col-md-1 col-12 mb-2 text-md-right text-end">
                <button type="button" class="btn btn-danger btn-sm removeRow">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
                $("#dynamicFormContainer").append(row);
            });

            $(document).on('click', '.removeRow', function() {
                if ($('.template-row').length > 1) {
                    $(this).closest('.template-row').remove();
                } else {
                    alert("At least one form must remain.");
                }
            });

            // Form validation
            $('#createDietForm').validate({
                rules: {
                    name: {
                        required: true
                    },
                    'title[]': {
                        required: true
                    },
                    'description[]': {
                        required: true
                    },
                    'time[]': {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter template name"
                    },
                    'title[]': {
                        required: "Please enter title"
                    },
                    'description[]': {
                        required: "Please enter description"
                    },
                    'time[]': {
                        required: "Please enter time"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.col').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // AJAX submit
            $('#createDietForm').on('submit', function(e) {
                e.preventDefault();
                if ($(this).valid()) {
                    let formData = new FormData(this);
                    $.ajax({
                        url: '/api/dietchart',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#dietSuccess').text(response.message ||
                                'Diet Chart created successfully!').show();
                            $('#createDietForm')[0].reset();
                            setTimeout(() => {
                                window.location.href = "{{ route('dietchart.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            let errorMessage = '';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            } else {
                                errorMessage = 'Something went wrong.';
                            }
                            $('#dietError').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
