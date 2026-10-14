@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/assessment-create.css') }}">

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title treatment-title">Add Assessment</h4>
                </div>
                @if (app('hasPermission')(28, 'view'))
                    <div class="col-sm-6 col-6 assessment-button m-b-2">
                        <a href="{{ route('assessment.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>


            <div class="assessment-entry rounded p-3 mb-4 position-relative">
                <div class="row">
                    <div class="col-12">
                        <form id="createAssessmentForm" class="form-container mx-auto"
                            style="width:60%; padding-bottom: 60px;" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="branch_id" id="branch_id">

                            <div class="container mt-4">

                                {{-- Template Name (only once) --}}
                                <div class="form-group">
                                    <label><i class="fas fa-file-signature"></i> Template Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control">
                                </div>

                                {{-- Dynamic form container --}}
                                <div id="dynamicFormContainer">
                                    <div class="form-row row mb-3 template-row align-items-end">

                                        <div class="col-md-4 col-12 mb-2">
                                            <label><i class="fas fa-heading"></i> Title <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title[]" class="form-control">
                                        </div>

                                        <div class="col-md-4 col-12 mb-2">
                                            <label><i class="fas fa-align-left"></i> Description <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="description[]" class="form-control">
                                        </div>

                                        <div class="col-md-3 col-12 mb-2">
                                            <label><i class="fas fa-image"></i> Image </label>
                                            <input type="file" name="image[]" class="form-control" accept="image/*">
                                        </div>

                                        <div class="col-md-1 col-12 mb-2 text-md-left text-center">
                                            <button type="button" class="btn btn-primary btn-sm addRow">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Success / Error Messages --}}
                                <div id="assesmentsuccessMessage" class="alert alert-success" style="display:none;"></div>
                                <div id="assesmenterrorMessage" class="alert alert-danger" style="display:none;"></div>

                                {{-- Submit Button --}}
                                <div class="m-t-20 text-center">
                                    <button type="submit" class="btn btn-primary submit-btn">Save Template</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <!-- JS + Select2 + jQuery Validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let storedBranchId = localStorage.getItem('selectedBranchId');
            if (storedBranchId) {
                document.getElementById('branch_id').value = storedBranchId;
            }
        });

        $(document).ready(function() {
            // Add more
            $(document).on('click', '.addMoreAssessment', function() {
                let $currentEntry = $(this).closest('.assessment-entry');
                let $clone = $currentEntry.clone();

                // Reset form inputs
                $clone.find('input, textarea, select').val('');
                $clone.find('.is-invalid').removeClass('is-invalid');
                $clone.find('.invalid-feedback').remove();
                $clone.find('.alert').hide();

                // Insert clone
                $clone.insertAfter($currentEntry);

                // Re-check remove button visibility
                updateRemoveButtons();
            });

            // Remove entry
            $(document).on('click', '.remove-entry', function() {
                $(this).closest('.assessment-entry').remove();

                // Re-check remove button visibility
                updateRemoveButtons();
            });

            // Initial check
            updateRemoveButtons();

            function updateRemoveButtons() {
                let totalForms = $('.assessment-entry').length;

                // If only one form, hide all remove buttons
                if (totalForms <= 1) {
                    $('.remove-entry').hide();
                } else {
                    $('.remove-entry').show();
                }
            }
        });
    </script>

    <script>
        // Validation
        $('#createAssessmentForm').validate({
            rules: {
                name: {
                    required: true
                },
                'title[]': {
                    required: true
                },
                'description[]': {
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
            },
        });
        $(document).ready(function() {

            // Add new row
            $(document).ready(function() {
                // Add new row
                $(".addRow").on("click", function() {
                    let row = `
                    <div class="form-row row mb-3 template-row align-items-end">
                        <div class="col-md-4 col-12 mb-2">
                            <label><i class="fas fa-heading"></i> Title <span class="text-danger">*</span></label>
                            <input type="text" name="title[]" class="form-control" required>
                        </div>
                        <div class="col-md-4 col-12 mb-2">
                            <label><i class="fas fa-align-left"></i> Description <span class="text-danger">*</span></label>
                            <input type="text" name="description[]" class="form-control" required>
                        </div>
                        <div class="col-md-3 col-12 mb-2">
                            <label><i class="fas fa-image"></i> Image <span class="text-danger">*</span></label>
                            <input type="file" name="image[]" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-1 col-12 mb-2 text-md-right text-end">
                            <button type="button" class="btn btn-danger btn-sm removeRow">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>`;

                    $("#dynamicFormContainer").append(row);
                });

                // Remove row
                $(document).on("click", ".removeRow", function() {
                    $(this).closest(".template-row").remove();
                });
            });

            // Remove row but keep at least one
            $(document).on('click', '.removeRow', function() {
                if ($('.template-row').length > 1) {
                    $(this).closest('.template-row').remove();
                } else {
                    alert("At least one form must remain.");
                }
            });



        });
    </script>


    <script>
        $(document).ready(function() {

            // Submit form
            $('#createAssessmentForm').on('submit', function(e) {
                e.preventDefault();
                if ($('#createAssessmentForm').valid()) {
                    let formData = new FormData(this);

                    $.ajax({
                        url: '/api/assessment', // your actual route
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        }, // if using auth
                        success: function(response) {
                            $('#assesmentsuccessMessage').text(response.message ||
                                'Assessment created successfully!').show();
                            $('#createAssessmentForm')[0].reset();
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('assessment.index') }}";
                            }, 1500);
                        },
                        error: function(xhr) {
                            var errorMessage = '';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, messages) {
                                    errorMessage += messages[0] + '<br>';
                                });
                            } else {
                                errorMessage = 'Something went wrong.';
                            }
                            $('#assesmenterrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
