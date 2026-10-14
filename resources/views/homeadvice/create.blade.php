@extends('layout.app')

<style>
    .treatment-title {
        padding-left: 95px !important;
        text-align: center !important;
    }

    .treatment-button {
        padding-right: 65px !important;
        text-align: center !important;
    }

    button.btn.btn-primary.btn-sm.addRow {
        margin-bottom: 40px;
    }

    a.btn.btn-primary.btn-rounded {
        margin-left: 267px;
    }

    h4.page-title.treatment-title {
        margin-left: 21px;
    }
    .form-container {
        width: 60% !important;
        padding-bottom: 60px !important;
    }


    @media screen and (max-width: 767px) {
        .treatment-title {
            padding-left: 0px !important;
            text-align: left !important;
        }

        .page-title {
            font-size: 19px !important;
        }

        .treatment-button {
            padding-right: 15px !important;
            text-align: right !important;
        }

        .add-btn-container {
            text-align: right;
        }

        button.btn.btn-primary.btn-sm.addRow {
            display: flex;
            margin-top: 10px;
            margin-bottom: 4px;
        }
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title treatment-title">Add Home Advice</h4>
                </div>
                @if (app('hasPermission')(29, 'view'))
                    <div class="col-sm-6 col-6 assessment-button m-b-2">
                        <a href="{{ route('homeadvice.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>



            <div class="homeadvice-entry rounded p-3 mb-4 position-relative">
                <div class="row">
                    <div class="col-12">
                        <form id="createhomeadviceForm" class="form-container mx-auto" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="branch_id" id="branch_id">

                            <div class="container mt-4">

                                {{-- Template Name (only once) --}}
                                <div class="form-group">
                                    <label><i class="fas fa-file-signature"></i> Template Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="template_name" class="form-control">
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
                                <div id="homeadvicesuccessMessage" class="alert alert-success" style="display:none;"></div>
                                {{-- <div id="homeadviceerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

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
        document.addEventListener('DOMContentLoaded', function () {
    let storedBranchId = localStorage.getItem('selectedBranchId');
    if (storedBranchId) {
        document.getElementById('branch_id').value = storedBranchId;
    }
});
        $(document).ready(function() {
            // Add more
            $(document).on('click', '.addMorehomeadvice', function() {
                let $currentEntry = $(this).closest('.homeadvice-entry');
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
                $(this).closest('.homeadvice-entry').remove();

                // Re-check remove button visibility
                updateRemoveButtons();
            });

            // Initial check
            updateRemoveButtons();

            function updateRemoveButtons() {
                let totalForms = $('.homeadvice-entry').length;

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
        $('#createhomeadviceForm').validate({
            rules: {
                'template_name': {
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
                'template_name': {
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
            // Add new row
            // Add new row
            $(document).on('click', '.addRow', function() {
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
                        <div class="col-md-1 col-12 mb-2 text-end">
                            <button type="button" class="btn btn-danger btn-sm removeRow">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>`;
                $('#dynamicFormContainer').append(row);
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
            $('#createhomeadviceForm').on('submit', function(e) {
                e.preventDefault();
                if ($('#createhomeadviceForm').valid()) {
                    let formData = new FormData(this);

                    $.ajax({
                        url: '/api/homeadvice', // your actual route
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        }, // if using auth
                        success: function(response) {
                            $('#homeadvicesuccessMessage').text(response.message ||
                                'homeadvice created successfully!').show();
                            $('#createhomeadviceForm')[0].reset();
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('homeadvice.index') }}";
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
                            $('#homeadviceerrorMessage').html(errorMessage).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection
