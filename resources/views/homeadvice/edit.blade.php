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

    button.btn.btn-danger.btn-sm.removeRow.ml-2 {
        margin-bottom: 43px;
        border-radius: 19px;
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
    }
</style>

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-sm-6 col-6">
                    <h4 class="page-title treatment-title">Edit Home Advice</h4>
                </div>
                @if (app('hasPermission')(29, 'view'))
                    <div class="col-sm-6 col-6 homeadvice-button m-b-2">
                        <a href="{{ route('homeadvice.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                            <i class="fa fa-arrow-left m-r-5 icon3"></i> <span class="hdr-btn-text">Back</span>
                        </a>
                    </div>
                @endif
            </div>


            <div class="homeadvice-entry rounded p-3 mb-4 position-relative">
                <div class="row">
                    <div class="col-12">
                        <form id="createhomeadviceForm" class="form-container" enctype="multipart/form-data">
                            @csrf

                            <div class="container mt-4">

                                {{-- Template Name (only once) --}}
                                <div class="form-group">
                                    <label><i class="fas fa-file-signature"></i> Template Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="template_name" class="form-control">
                                </div>

                                {{-- Dynamic form container --}}
                                <div id="dynamicFormContainer">
                                    <div class="form-row d-flex align-items-center mb-3 template-row">

                                        <div class="col">
                                            <label><i class="fas fa-heading"></i> Title <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title[]" class="form-control">
                                        </div>

                                        <div class="col">
                                            <label><i class="fas fa-align-left"></i> Description <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="description[]" class="form-control">
                                        </div>

                                        <div class="col">
                                            <label><i class="fas fa-image"></i> Image </label>
                                            <input type="file" name="image[]" class="form-control" accept="image/*">
                                        </div>

                                        <div class="col-auto d-flex align-items-center">
                                            <button type="button" class="btn btn-primary btn-sm addRow">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Success / Error Messages --}}
                                <div id="edithomeadvicesuccessMessage" class="alert alert-success" style="display:none;">
                                </div>
                                {{-- <div id="edithomeadviceerrorMessage" class="alert alert-danger" style="display:none;"></div> --}}

                                {{-- Submit Button --}}
                                <div class="m-t-20 text-center">
                                    <button type="submit" class="btn btn-primary submit-btn">Save Template</button>
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
        const IMAGE_PATH = "{{ url('/') }}/"; // Laravel app base URL
        $(document).ready(function() {
            let homeadviceId = {{ $id }};

            // Fetch existing homeadvice data
            $.ajax({
                url: `/api/homeadvice/${homeadviceId}`,
                method: 'GET',
                success: function(data) {
                    $('input[name="template_name"]').val(data.template_name);
                    $('#dynamicFormContainer').empty();

                    let items = data.items || [];

                    if (items.length > 0) {
                        items.forEach(function(item) {
                            appendRow(item.title, item.description, item.image);
                        });
                    } else {
                        appendRow(); // add empty row if no data
                    }

                    toggleRemoveButtons();
                },
                error: function() {
                    alert('Failed to load homeadvice data.');
                }
            });

            // Function to add a row
            function appendRow(title = '', description = '', imagePath = '') {
                let imagePreview = '';
                let uniqueId = 'preview-' + Math.random().toString(36).substr(2, 9);

                let APP_ENV = "{{ app()->environment() }}";
                let IMAGE_PATH = "{{ url('/') }}/";

                if (imagePath) {
                    let cleanPath = imagePath.replace(/^\/?/, '');
                    if (APP_ENV === 'production' && !cleanPath.startsWith('public/')) {
                        cleanPath = 'public/' + cleanPath;
                    }
                    imagePreview = `<img id="${uniqueId}" src="${IMAGE_PATH}${cleanPath}"
                             alt="Existing Image" class="img-thumbnail mb-2" width="50">`;
                } else {
                    imagePreview = `<img id="${uniqueId}" src="https://via.placeholder.com/80"
                             alt="Preview" class="img-thumbnail mb-2">`;
                }

                let row = `
                <div class="form-row d-flex align-items-center mb-3 template-row">
                    <div class="col">
                        <label><i class="fas fa-heading"></i> Title <span class="text-danger">*</span></label>
                        <input type="text" name="title[]" class="form-control" value="${title}">
                    </div>
                    <div class="col">
                        <label><i class="fas fa-align-left"></i> Description <span class="text-danger">*</span></label>
                        <input type="text" name="description[]" class="form-control" value="${description}">
                    </div>
                    <div class="col-md-3">
                        <label><i class="fas fa-image"></i> Image <span class="text-danger">*</span></label>
                        <input type="file" name="image[]" class="form-control image-input"
                               accept="image/*" data-preview-id="${uniqueId}">
                    </div>
                    ${imagePreview}
                    <div class="col-auto d-flex align-items-center">
                        <button type="button" class="btn btn-primary btn-sm addRow" title="Add More">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm removeRow ml-2" title="Remove">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>`;
                $('#dynamicFormContainer').append(row);
                toggleRemoveButtons();
            }

            function toggleRemoveButtons() {
                const totalRows = $('.template-row').length;
                if (totalRows <= 1) {
                    $('.removeRow').hide();
                } else {
                    $('.removeRow').show();
                }
            }

            // Image preview on file change
            $(document).on('change', '.image-input', function(event) {
                let previewId = $(this).data('preview-id');
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#' + previewId).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Add new row
            $(document).on('click', '.addRow', function() {
                appendRow();
            });

            // Remove row (keep at least one)
            $(document).on('click', '.removeRow', function() {
                if ($('.template-row').length > 1) {
                    $(this).closest('.template-row').remove();
                    toggleRemoveButtons();
                } else {
                    alert("At least one row is required.");
                }
            });

            // Submit updated data
            $('#createhomeadviceForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: `/api/homeadvice/${homeadviceId}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT',
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function() {
                        $('#edithomeadvicesuccessMessage').text(
                            'homeadvice updated successfully!').show();
                        $('#edithomeadviceerrorMessage').hide();
                        setTimeout(() => window.location.href = '/homeadvice', 1500);

                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors || {};
                        let errorMsg = '';
                        $.each(errors, function(key, messages) {
                            errorMsg += messages.join(', ') + '<br>';
                        });
                        $('#edithomeadviceerrorMessage').html(errorMsg).show();
                        $('#edithomeadvicesuccessMessage').hide();
                    }
                });
            });
        });
    </script>
@endsection
