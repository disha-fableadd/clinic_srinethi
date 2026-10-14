@extends('layout.app')
@section('content')
<style>
    .page-title {
        text-align: center !important;
        padding-left: 62px !important;
    }
    .form-container {
        width: 70% ;
        padding-bottom: 60px !important;
    }

    .select2-container--default .select2-selection--multiple {
        box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 50px !important;
        height: 40px !important;
        border-color: rgb(207, 236, 224) !important;
        padding-top: 4px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: rgb(207, 236, 224) !important;
        color: black !important;
        border: none !important;
        font-size: 14px;
        font-weight: normal !important;
        margin-top: 5px !important;
        margin-right: 5px !important;
        display: inline-block !important;
        float: left !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: block !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        text-align: left !important;
        padding-left: 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 1px !important;
        right: 10px !important;
    }

    .form-control {
        border-radius: 50px !important;
        height: 40px !important;
        border-color: rgb(207, 236, 224) !important;
        padding-left: 15px !important;
    }


    @media screen and (max-width:767px) {

        .page-title {
            font-size: 16px !important;
            margin-top: 7px !important;
            padding-left: 0px !important;

        }

        .service-btn {
            text-align: right !important;
            padding-right: 15px !important;

        }
          .form-container {
        width: 100% !important;
        padding-bottom: 60px !important;
    }
    }
</style>
<div class="page-wrapper">
    <div class="content" style="height:100vh;">
        <div class="row" style="padding-top:15px">
            <div class="col-sm-8 col-6">
                <h4 class="page-title">Edit Radiology Test</h4>
            </div>
             @if(app('hasPermission')(21, 'view'))
            <div class="col-sm-4 col-6 service-btn">
                <a href="{{ route('radiology-tests.index') }}" class="btn btn-primary btn-rounded btn-hdr">
                    <i class="fa fa-arrow-left m-r-5"></i> <span class="hdr-btn-text">Back</span>
                </a>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="offset-lg-2">
                <form id="editRadiologyTestForm" class="form-container" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Fields will be filled by JS -->
                    <div class="form-group">
                        <input type="hidden" name="id" id="id">
                        <label><i class="fas fa-vial icon-style"></i> Test Name <span
                                                        class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="test_name" id="test_name">
                        <span class="invalid-feedback" id="error_test_name"></span>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><i class="fas fa-barcode icon-style"></i> Test Code <span
                                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="test_code" id="test_code">
                                <span class="invalid-feedback" id="error_test_code"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><i class="fas fa-user-md icon-style"></i> Body Part <span
                                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="body_part" id="body_part">
                                <span class="invalid-feedback" id="error_body_part"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><i class="fas fa-file-alt icon-style"></i> Report Format File Type</label>
                                <select class="form-control select2" name="report_format" id="report_format">
                                    <option value="">Select Format</option>
                                    <option value="dcm">DICOM (.dcm)</option>
                                    <option value="jpg">JPEG (.jpg)</option>
                                    <option value="jpeg">JPEG (.jpeg)</option>
                                    <option value="png">PNG (.png)</option>
                                    <option value="tiff">TIFF (.tiff)</option>
                                    <option value="bmp">Bitmap (.bmp)</option>
                                    <option value="pdf">PDF (.pdf)</option>
                                    <option value="doc">Word (.doc)</option>
                                    <option value="docx">Word (.docx)</option>
                                    <option value="webp">WEBP (.webp)</option>
                                    <option value="avi">Video (.avi)</option>
                                    <option value="mp4">Video (.mp4)</option>
                                    <option value="zip">ZIP (.zip)</option>
                                </select>
                                <span class="invalid-feedback" id="error_report_format"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><i class="fas fa-dollar-sign icon-style"></i> Cost <span
                                                        class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="cost" id="cost" step="0.01">
                                <span class="invalid-feedback" id="error_cost"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><i class="fas fa-percent icon-style"></i> GST Option</label>
                                <select class="form-control select2" name="gst_option" id="gst_option">
                                    <option value="">Select GST Option</option>
                                    <option value="Without GST">Without GST</option>
                                    <option value="With GST">With GST</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="product_gst_wrapper" style="display:none;">
                            <div class="form-group">
                                <label><i class="fas fa-percentage icon-style"></i> Product GST</label>
                                <select class="form-control select2" name="product_gst[]" id="product_gst" multiple>
                                    <option value="">Select GST</option>
                                </select>
                            </div>
                        </div>
                    </div>

                   

                    <!-- Alerts -->
                    <div id="editradiotestsuccessMessage" class="alert alert-success" style="display:none;"></div>
                    <div id="editradiotesterrorMessage" class="alert alert-danger" style="display:none;"></div>

                    <div class="m-t-20 text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Update Radiology Test</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Include in your <head> section -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include before </body> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function getIdFromUrl() {
        const segments = window.location.pathname.split('/');
        return segments.pop() || segments.pop();
    }

    function showCurrentFile(filePath) {
        if (!filePath) {
            $('#currentFile').html('<span>No file uploaded.</span>');
            return;
        }
        let ext = filePath.split('.').pop().toLowerCase();
        let fileUrl = filePath.startsWith('uploads/') ? '/' + filePath : '/storage/' + filePath;
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            $('#currentFile').html(`<img src=\"${fileUrl}\" alt=\"Current File\" style=\"max-width:150px;max-height:150px;display:block;margin-bottom:10px;\">`);
        } else if (ext === 'pdf') {
            $('#currentFile').html(`<a href=\"${fileUrl}\" target=\"_blank\" class=\"btn btn-info btn-sm\">View PDF</a> <a href=\"${fileUrl}\" download class=\"btn btn-success btn-sm\">Download</a>`);
        } else {
            $('#currentFile').html(`<a href=\"${fileUrl}\" target=\"_blank\" class=\"btn btn-info btn-sm\">View File</a> <a href=\"${fileUrl}\" download class=\"btn btn-success btn-sm\">Download</a>`);
        }
    }

    $(document).ready(function() {
        $('#report_format').select2({
            placeholder: 'Select Format',
            width: '100%'
        });

        $('#gst_option').select2({
            placeholder: 'Select GST Option',
            width: '100%'
        });

        $('#product_gst').select2({
            placeholder: 'Select GST',
            width: '100%'
        });

        // Flag to prevent redundant fetchTaxes during initial data load
        let isLoadingInitialData = true;

        // Toggle Product GST field visibility
        $('#gst_option').on('change', function() {
            if ($(this).val() === 'With GST') {
                $('#product_gst_wrapper').show();
                if (!isLoadingInitialData) {
                    fetchTaxes();
                }
            } else {
                $('#product_gst_wrapper').hide();
                $('#product_gst').val(null).trigger('change');
            }
        });

        function fetchTaxes(callback) {
            let branchId = localStorage.getItem('selectedBranchId');
            $.ajax({
                url: '/api/tax-rates',
                type: 'GET',
                data: {
                    branch_id: branchId
                },
                success: function(response) {
                    let gstSelect = $('#product_gst');
                    gstSelect.empty(); // No need for "Select GST" placeholder option in multiple select
                    $.each(response, function(index, tax) {
                        if (tax.status === 'active') {
                            let taxValue = `${tax.tax_name} (${tax.tax_rate}%)`;
                            gstSelect.append(`<option value="${taxValue}">${taxValue}</option>`);
                        }
                    });
                    
                    if (typeof callback === 'function') {
                        callback();
                    } else {
                        gstSelect.trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching taxes:', xhr);
                }
            });
        }

        const id = getIdFromUrl();
        $.ajax({
            url: `/api/radiology-tests/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#id').val(data.id || '');
                $('#test_name').val(data.test_name || '');
                $('#test_code').val(data.test_code || '');
                $('#body_part').val(data.body_part || '');
                $('#cost').val(data.cost || '');
                $('#report_format').val(data.report_format || '').trigger('change');

                if (data.gst_option) {
                    // Normalize GST option based on expected value "With GST" or "Without GST"
                    let normalizedGstOption = data.gst_option.toLowerCase().indexOf('with') !== -1 && data.gst_option.toLowerCase().indexOf('without') === -1 ? 'With GST' : 'Without GST';
                    $('#gst_option').val(normalizedGstOption).trigger('change');
                    
                    if (normalizedGstOption === 'With GST') {
                        fetchTaxes(function() {
                            if (data.product_gst) {
                                // data.product_gst should be an array because of Laravel cast
                                $('#product_gst').val(data.product_gst).trigger('change');
                            }
                            isLoadingInitialData = false;
                        });
                    } else {
                        isLoadingInitialData = false;
                    }
                } else {
                    isLoadingInitialData = false;
                }
            },
            error: function() {
                isLoadingInitialData = false;
            }
        });
    });
    $('#editRadiologyTestForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous errors and messages
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('#editradiotestsuccessMessage').hide().text('');
        $('#editradiotesterrorMessage').hide().text('');

        let form = new FormData(this);
        let testId = $('#id').val();

        // Simulate PUT for Laravel
        form.append('_method', 'PUT');

        $.ajax({
            url: '/api/radiology-tests/' + testId,
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editradiotestsuccessMessage').text(response.message).show();

                // Optional redirect after delay
                setTimeout(() => {
                    window.location.href = '/radiology-tests';
                }, 2000);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, messages) {
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.after('<span class="invalid-feedback" style="color:#e74c3c;font-size:0.95em;">' + messages[0] + '</span>');
                    });
                } else {
                    $('#editradiotesterrorMessage').text('Something went wrong. Please try again.').show();
                }
            }
        });
    });


</script>
@endsection